<?php namespace App\Controllers;

// Include the necessary classes
use App\Services\Session;
use App\Modules\Users\Models\User;
use App\Services\View;

// Base Controller
class Controller
{
    public function __construct(Session $SESSION, $Function)
    {
        $this->SESSION = $SESSION;
    }

    public function index ()
    {
        // Set some page vars
        $User = new User;

        $Vars = compact('User');
        View::Make('index', $Vars);
    }

    public function Page ($Vars)
    {
        $Page = false;

        if (!$Page) { View::Make('NotFound'); }
        die('<pre>'.print_r($Vars, true).'</pre>');
    }
}
?>