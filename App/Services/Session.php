<?php namespace App\Services;

use App\Models\Model;
use App\Modules\Users\Models\User;

/**
 * Class Session
 * @package App\Services
 */
class Session
{
    /**
     * Whether or not the user is logged in
     *
     * @var bool
     */
    private $LoggedIn = false;

    /**
     * The logged in user's id
     *
     * @var int
     */
    public $UserID;

    /**
     * The logged in user's role
     *
     * @var int|string
     */
    public $Role;

    /**
     * The message to the user usually a success message after a form submit
     *
     * @var string
     */
    private $message;

    /**
     * This is the type of message
     *
     * @var string success|danger|warning|info
     */
    private $messageType;

    /**
     * The id column of the user table
     *
     * @var string
     */
    private $userId = 'id';

    /**
     * The role column of the user table
     *
     * @var string
     */
    private $userRole = 'role';

    /**
     * The current session
     *
     * @var object Session
     */
    private static $SESSION;

    /**
     * Session constructor.
     */
    public function __construct ()
    {
        session_start ();
        $this->CheckMessage ();
        $this->CheckLogin ();
    }

    /**
     * Get the current session if there is one on create a new one if not
     *
     * @return Session
     */
    public static function GetTheSession ()
    {
        if (!self::$SESSION) { self::$SESSION = new self; }
        return self::$SESSION;
    }

    /**
     * Checks if the user is supposed to be logged in and logs them in if they are
     */
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

    /**
     * Checks for a message and grabs it if it's there and deletes the $_SESSION[message] var
     */
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

    /**
     * Checks if the user is logged in
     *
     * @return bool
     */
    public function IsLoggedIn ()
    {
        return $this->LoggedIn;
    }

    /**
     * Checks if the user is authorized based on being logged in and their role
     *
     * @param string $role
     *
     * @return bool
     */
    public function IsAuthorized ($role)
    {
        return $this->LoggedIn && strpos ($this->Role, '|'.$role.'|') !== false;
    }

    /**
     * Redirect the user to another page if unauthorized
     *
     * @param array|string $role
     * @param string $url
     */
    public function CheckAuthorization ($role, $url)
    {
        if (is_array($role))
        {
            $Authorized = false;
            foreach ($role as $v) { if ($this->IsAuthorized($v)) { $Authorized = true; } }
            if (!$Authorized) { Redirect($url); }
        }
        else { if (!$this->IsAuthorized($role)) { Redirect($url); } }
    }

    /**
     * Logs the user in
     *
     * @param object $user
     * @param bool $remember
     */
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

    /**
     * Logs the user out and redirects the user
     *
     * @param string $Redirect url
     */
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
        Redirect ($Redirect?$Redirect:SITE_URL);
    }

    /**
     * Gets or sets the message
     *
     * @param string $msg
     * @param string $type
     *
     * @return mixed
     */
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

    /**
     * Gets or sets the message type
     *
     * @param string $type
     *
     * @return mixed
     */
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
