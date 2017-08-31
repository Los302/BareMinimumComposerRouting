<?php namespace App\Modules\Users\Models;

use App\Models\Model;
use Includes\Upload;

class User extends Model
{
    protected static $TableName = 'users';
    protected $Fields = ['id', 'username', 'password', 'first_name', 'last_name', 'email', 'role', 'active'];

    public $id;
    public $username;
    public $password;
    public $first_name;
    public $last_name;
    public $email;
    public $role;
    public $active;
    
    public static $Users = [];
    
    private $RawPassword = false;
    private $DefaultAvatarLocation = 'unknown_profile.png';

    public $rules = [
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required|email',
        'username' => 'required|unique:User,Check4DuplicateUN',
        'password' => 'string|min:6|max:32'
    ];

    public $messages = [
        'first_name' => ['required' => 'First Name is required'],
        'last_name' => ['required' => 'Last Name is required'],
        'email' => [
            'required' => 'Email is required',
            'email' => 'Please enter a valid email'
        ],
		'username' => [
			'required' => 'Username is required',
			'unique' => 'Username is already in use and must be unique'
		],
        'password' => [
            'min' => 'Password must be between 6 and 32 characters',
            'max' => 'Password must be between 6 and 32 characters'
        ]
    ];

    public function GetUserInput ($Input)
    {
        $this->first_name = isset ($Input['first_name']) ? NoScript($Input['first_name']) : $this->first_name;
        $this->last_name = isset ($Input['last_name']) ? NoScript($Input['last_name']) : $this->last_name;
        $this->email = isset ($Input['email']) ? NoScript_NoQuotes($Input['email']) : $this->email;
        $this->username = isset ($Input['username']) ? NoScript_NoQuotes($Input['username']) : $this->username;
        if (isset ($Input['password'])) { $this->password = $Input['password']; }
        elseif (!$this->RawPassword) { $this->password = self::DecryptThis($this->password, KEY2); }
        $this->RawPassword = true;
    }

    public static function FindIt ($id)
    {
        $it = self::find_by_sql('SELECT * FROM '.self::$TableName.' WHERE id = :id', [':id' => $id]);
        return array_shift ($it);
    }

    public static function FindActiveUser ($ID)
    {
        $q = 'SELECT * FROM '.self::$TableName.' WHERE id = :id AND active > 0';
        $User = isset(self::$Users[$ID]) ? self::$Users[$ID] : self::find_by_sql($q, [':id' => $ID]);
        if (!isset(self::$Users[$ID]) && !empty ($User))
        {
			$User = array_shift ($User);
			self::$Users[$User->id] = $User;
		}
        return $User;
    }
    
    public static function FindByUsername ($un)
    {
        $q = 'SELECT * FROM '.self::$TableName.' WHERE username = :un';
        $r = self::find_by_sql ($q, [':un' => $un]);//die('<pre>'.print_r($q, true).'</pre>');
        
        $Return = false;
        if (!empty ($r))
        {
			$User = array_shift ($r);
			$Return = self::$Users[$User->id] = $User;
		}
        return $Return;
    }

    public static function Authenticate ($un, $pw, $role)
    {
        $pw = self::EncryptThis ($pw, KEY2);
        $q = 'SELECT * FROM '.self::$TableName.'
							WHERE username = :un
							AND `password` = :pw
							AND role LIKE :role
							AND active > 0';
        $r = self::find_by_sql ($q, [':un' => $un, ':pw' => $pw, ':role' => '%|'.$role.'|%']);//die('<pre>'.print_r($q, true).'</pre>');

        $Return = false;
        if (!empty ($r))
        {
			$User = array_shift ($r);
			$Return = self::$Users[$User->id] = $User;
		}
        return $Return;
    }

    public static function Check4DuplicateUN ($UN, $ExceptID = 0)
    {
        $where = 'username = :un AND id != :id';
        $r = self::count($where, [':un' => $UN, ':id' => $ExceptID]);
        return $r;
    }

    public function AvatarLocation ($URL = true)
    {
        $loc = $this->avatar ? $this->avatar : IMAGE_URL.$this->DefaultAvatarLocation;
        if (false === strpos($loc, '//'))
        {
            $root = $URL ? IMAGE_URL : IMAGE_PATH;
            $loc = $root.'Users/'.$loc;
        }
        return $loc;
    }

    public function DeleteImage ()
    {
        $loc = $this->AvatarLocation(false);
        if ($this->avatar && FALSE === strpos($this->avatar, '//') && file_exists($loc)) { unlink($loc); }
    }

    public function save ()
    {
        if ($this->RawPassword)
        {
            $this->password = self::EncryptThis($this->password, KEY2);
            $this->RawPassword = false;
        }
        return parent::save();
    }

    public function EmailPassword ()
    {
        $To = (!empty($this->first_name) ? $this->first_name.' '.$this->last_name : '').'<'.$this->email.'>';
        $Subject = 'Your requested info';
        $Message = 'Hello '.$this->first_name.',<br /><br />Here is your password:<br /><br />'.self::DecryptThis($this->password, KEY2);
        $Headers = 'From: '.SITE_NAME.' <no-reply@'.SITE_URL.'>'."\r\n";
        $Headers .= 'MIME-Version: 1.0' . "\r\n";
        $Headers .= 'Content-type:text/html;charset=UTF-8' . "\r\n";
        mail ($To, $Subject, $Message, $Headers);
    }
}
?>
