<?php namespace App\Services;

use App\Models\Model;
use App\Modules\Users\Models\User;

class Session
{
    private $LoggedIn = false;
    public $UserID;
    public $Role;
    private $message;
    private $messageType;

    private $userId = 'id';
    private $userRole = 'role';

    private static $SESSION;

    public function __construct ()
    {
        session_start ();
        $this->CheckMessage ();
        $this->CheckLogin ();
    }

    public static function GetTheSession ()
    {
        if (!self::$SESSION) { self::$SESSION = new self; }
        return self::$SESSION;
    }

    public function CheckLogin ()
    {
        if (isset ($_SESSION['UserID']))
        {
            $this->UserID = $_SESSION['UserID'];
            $this->Role = $_SESSION['Role'];
            $this->LoggedIn = true;
        }
        else
        {
            if (isset ($_COOKIE['UserID']))
            {
                $UserID = (int)Model::DecryptThis ($_COOKIE['UserID'], KEY1); # Decrypt value to find Member ID
                $User = User::FindActiveUser ($UserID);

                if ($User) { $this->LogIn($User); }
                else
                {
                    $NegativeTime	= time () - 3600;
                    setcookie ('UserID', '', $NegativeTime, '/');
                }
            }
            else
            {
                $this->LoggedIn = false;
                unset ($this->UserID);
                $this->Role = '';
            }
        }
    }

    private function CheckMessage ()
    {
        if (isset ($_SESSION['message']))
        {
            $this->message = $_SESSION['message'];
            unset ($_SESSION['message']);
        }
        else
        {
            $this->message = '';
        }
    }

    public function IsLoggedIn ()
    {
        return $this->LoggedIn;
    }

    public function IsAuthorized ($role)
    {
        return $this->LoggedIn && strpos ($this->Role, '|'.$role.'|') !== false;
    }

    public function CheckAuthorization ($role, $url)
    {
        if (is_array($role))
        {
            $Authorized = false;
            foreach ($role as $v) { if ($this->IsAuthorized($v)) { $Authorized = true; } }
            if (!$Authorized) { redirect_to($url); }
        }
        else { if (!$this->IsAuthorized($role)) { redirect_to($url); } }
    }

    public function LogIn ($user, $remember = false)
    {
        $id = $this->userId;
        $Role = $this->userRole;
        $this->UserID = $_SESSION['UserID'] = $user->$id;
        $this->Role = $_SESSION['Role'] = $user->$Role;
        $this->LoggedIn = true;

        $encrypted_id = trim (Model::EncryptThis($user->$id, KEY1));
        $time = $remember ? time () + 2592000 * 12 : 0;// One year or nothing
        setcookie ( 'UserID', $encrypted_id, $time, '/' );
    }

    public function LogOut ($Redirect = '')
    {
        unset ($_SESSION['UserID']);
        unset ($_SESSION['Username']);
        unset ($_SESSION['Role']);

        $NegativeTime	= time () - 3600;
        setcookie ('UserID', '', $NegativeTime, '/');

        unset ($this->UserID);
        $this->LoggedIn = false;
        $this->Role = '';
        redirect_to ($Redirect?$Redirect:SITE_URL);
    }

    public function message ($msg = '', $type = '')
    {
        if (!empty ($msg))
        {
            $_SESSION['message'] = $this->message = $msg;
            if ($type) { $this->messageType($type); }
        }
        else
        {
            if (isset($_SESSION['message']) && $_SESSION['message'] == $this->message) { unset($_SESSION['message']); }
            return $this->message;
        }
    }

    public function messageType ($type = '')
    {
        if (!empty($type)) { $_SESSION['messageType'] = $type; }
        else
        {
            if (isset($_SESSION['messageType']))
            {
                $this->messageType = $_SESSION['messageType'];
                unset($_SESSION['messageType']);
            }
            return $this->messageType;
        }
    }
}
?>
