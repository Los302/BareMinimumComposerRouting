<?php namespace App\Services;

// Include the necessary classes
use eftec\bladeone;
use App\Services\Session;

/**
 * Class View
 * @package App\Services
 */
class View
{
    /**
     * This is where to find the view
     *
     * @var string
     */
    protected static $Views = 'App';

    /**
     * This is where to fine the cache files
     *
     * @var string
     */
    protected static $Cache = 'App/Views/Cache';

    /**
     * This will be the location of the view if it's in a module
     *
     * @var bool|string
     */
    public static $ModuleViews = false;

    /**
     * Get the view and send it to the visitor
     *
     * @param string $View
     * @param array $Vars
     *
     * @throws \Exception
     */
    public static function Make ($View, $Vars = [])
    {
        echo self::GetHTML($View, $Vars);
    }

    /**
     * Returns the view as a string
     *
     * @param string $View
     * @param array $Vars
     *
     * @return string
     *
     * @throws \Exception
     */
    public static function GetHTML ($View, $Vars = [])
    {
        $Vars['SESSION'] = Session::GetTheSession();
        $Template = new bladeone\BladeOne(SITE_ROOT.self::$Views, SITE_ROOT.self::$Cache);
        $View = self::$ModuleViews ? self::$ModuleViews.'.'.$View : 'Views.'.$View;
        return $Template->run($View, $Vars);
    }

    /**
     * Creates a json string from the vars and sends it to the visitor
     *
     * @param array $Vars
     */
    public static function JSON ($Vars)
    {
        $JSON = json_encode($Vars);
        echo($JSON);
    }
}
?>
