<?php namespace App\Controllers;

// Include the necessary classes
use App\Helpers\Session;
use App\Modules\Users\Models\User;
use App\Helpers\View;

// Base Controller

/**
 * Class Controller
 * @package App\Controllers
 */
class Controller
{
    /**
     * The current session
     *
     * @var Session
     */
    public $SESSION;

    /**
     * Controller constructor.
     * @param Session $SESSION
     * @param string $Method
     */
    public function __construct(Session $SESSION, $Method)
    {
        $this->SESSION = $SESSION;
    }

    /**
     * This is the index page of the site
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
     * @param array $Vars
     *
     * @throws \Exception
     */
    public function Page ($Vars)
    {
        $Page = false;

        if (!$Page) { View::Make('NotFound'); }
        echo ('<pre>'.print_r($Vars, true).'</pre>');
    }
}
?>