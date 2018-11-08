<?php namespace App\Controllers;

// Include the necessary classes
use App\Services\Session;
use App\Modules\Users\Models\User;
use App\Services\View;
use http\Env\Response;

// Base Controller

/**
 * Class Controller
 * @package App\Controllers
 */
class Controller
{
    /**
     * Controller constructor.
     * @param Session $SESSION
     * @param $Method
     */
    public function __construct(Session $SESSION, $Method)
    {
        $this->SESSION = $SESSION;
    }

    /**
     * This is the index page of the site
     *
     * @return Response
     */
    public function index ()
    {
        // Set some page vars
        $User = new User;

        $Vars = compact('User');
        View::Make('index', $Vars);
    }

    /**
     * This is a catch all page
     *
     * @param $Vars
     *
     * @return Response
     */
    public function Page ($Vars)
    {
        $Page = false;

        if (!$Page) { View::Make('NotFound'); }
        die('<pre>'.print_r($Vars, true).'</pre>');
    }
}
?>