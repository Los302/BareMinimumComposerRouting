<?php namespace App\Modules\Users\Controllers;

// Include the necessary classes
use App\Controllers\Controller;
use App\Services\Session;
use App\Modules\Users\Models\User;
use App\Services\View;

// Admin Controller
class Admin extends Controller
{
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

    public function index ()
    {
        // Show the page
        View::Make('index');
    }

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
                redirect_to(ADMIN_URL);
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
                redirect_to(ADMIN_URL.'Login');
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

    public function Logout ()
    {
        $SESSION = $this->SESSION;
        // Check for authorization
        if ($SESSION->IsLoggedIn ()) { $SESSION->LogOut (SITE_URL); }
    }
}
?>