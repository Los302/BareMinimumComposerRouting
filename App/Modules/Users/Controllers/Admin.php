<?php namespace App\Modules\Users\Controllers;

// Include the necessary classes
use App\Controllers\Controller;
use App\Services\Session;
use App\Modules\Users\Models\User;
use App\Services\View;

// Admin Controller

/**
 * Class Admin
 * @package App\Modules\Users\Controllers
 */
class Admin extends Controller
{
    /**
     * Admin constructor.
     * @param Session $SESSION
     * @param string $Method
     */
    public function __construct(Session $SESSION, $Method)
    {
        // Check for authentication
        $Allowed = ['Login', 'ForgotPassword'];
        if (!in_array($Method, $Allowed)) { $SESSION->CheckAuthorization('ADMIN', '/Admin/Login'); }
        //die('Line: '.__LINE__.'<br>Method: '.$Method.'<pre>'.print_r($_SESSION, true).'</pre>');

        // Set some vars
        $this->SESSION = $SESSION;
        View::$ModuleViews = 'Modules.Users.Views.Admin.';
    }

    /**
     * Show is the Admin home page
     *
     * @throws \Exception
     */
    public function index ()
    {
        // Show the page
        View::Make('index');
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
            $user = User::Authenticate($_POST['uname'], $_POST['pword'], 'ADMIN');
            if ($user)
            {
                $SESSION->LogIn($user);
                Redirect(ADMIN_URL);
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
            'UName' => $UName
        ];
        View::$ModuleViews = 'Modules.Users.Views.';
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
                Redirect(ADMIN_URL.'Login');
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
            'UName' => $UName
        ];
        View::$ModuleViews = 'Modules.Users.Views.';
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