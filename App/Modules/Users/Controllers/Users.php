<?php namespace App\Modules\Users\Controllers;

// Include the necessary classes
use App\Controllers\Controller;
use App\Services\Session;
use App\Modules\Users\Models\User;
use App\Services\View;

// User Controller

/**
 * Class Users
 * @package App\Modules\Users\Controllers
 */
class Users extends Controller
{
    /**
     * Users constructor.
     * @param Session $SESSION
     * @param string $Method
     */
    public function __construct(Session $SESSION, $Method)
    {
        // Check for authentication
        $Allowed = ['Login', 'ForgotPassword', 'Logout'];
        if (!in_array($Method, $Allowed)) { $SESSION->CheckAuthorization('USER', '/User/Login'); }

        // Set some vars
        $this->SESSION = $SESSION;
        View::$ModuleViews = 'Modules.Users.Views.';
    }

    /**
     * Show is the User home page
     *
     * @throws \Exception
     */
    public function index ()
    {
        // Show the page
        View::Make('Users.index');
    }

    /**
     * Attempt to log the user in or show the login page
     *
     * @throws \Exception
     */
    public function Login ()
    {
        $SESSION = $this->SESSION;
        $status = 'New';//<-- This is for AJAX
        $UName = '';

        // Check for post
        if (isset ($_POST['uname']))
        {
            // Authenticate the user
            $user = User::Authenticate($_POST['uname'], $_POST['pword']);
            if ($user)
            {
                $SESSION->LogIn($user);
                $Redirect = $SESSION->IsAuthorized('ADMIN') ? ADMIN_URL : USERS_URL;
                Redirect($Redirect);
                $status = 'Success';
            }
            else
            {
                $SESSION->message('Incorrect username and/or password', 'alert-danger');
                $UName = htmlspecialchars($_POST['uname']);
                $status = 'Fail';
            }
        }

        // Show the page
        $Vars = [
            'SESSION' => $SESSION,
            'UName' => $UName
        ];
        View::Make('login', $Vars);
    }

    /**
     * Show the forgot password page and/or email the pw to the user
     *
     * @throws \Exception
     */
    public function ForgotPassword ()
    {
        $SESSION = $this->SESSION;
        $status = 'New';//<-- This is for AJAX
        $UName = '';

        // Check for post
        if (isset ($_POST['uname']))
        {
            // Authenticate the user
            $user = User::FindByUsername($_POST['uname']);
            if ($user)
            {
                $user->EmailPassword();
                $SESSION->message('The password has been sent to the email address associated with that username.', 'alert-success');
                Redirect(USERS_URL.'Login');
                $status = 'Success';
            }
            else
            {
                $SESSION->message('Incorrect username', 'alert-danger');
                $UName = htmlspecialchars($_POST['uname']);
                $status = 'Fail';
            }
        }

        // Show the page
        $Vars = [
            'SESSION' => $SESSION,
            'UName' => $UName
        ];
        View::Make('ForgotPassword', $Vars);
    }

    /**
     * Log the user out and redirect the user
     */
    public function Logout ()
    {
        $SESSION = $this->SESSION;
        // Check for authorization
        if ($SESSION->IsLoggedIn ()) { $SESSION->LogOut (SITE_URL); }
    }
}
?>