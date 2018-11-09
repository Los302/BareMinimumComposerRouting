<?php namespace App\Services;

// Include the necessary classes
use eftec\bladeone;
use App\Services\Session;

class View
{
    protected static $Views = 'App';
    protected static $Cache = 'App/Views/Cache';
    public static $ModuleViews = false;
    public static $SESSION;

    public static function Make ($View, $Vars = [])
    {
        if ($View == 'JSON')
        {
            $JSON = json_encode($Vars);
            die($JSON);
        }

        $Vars['SESSION'] = Session::GetTheSession();
        $Template = new bladeone\BladeOne(SITE_ROOT.self::$Views, SITE_ROOT.self::$Cache);
        $View = self::$ModuleViews ? self::$ModuleViews.'.'.$View : 'Views.'.$View;
        die($Template->run($View, $Vars));
    }

    public static function GetHTML ($View, $Vars = [])
    {
        $Vars['SESSION'] = Session::GetTheSession();
        $Template = new bladeone\BladeOne(SITE_ROOT.self::$Views, SITE_ROOT.self::$Cache);
        $View = self::$ModuleViews ? self::$ModuleViews.'.'.$View : 'Views.'.$View;
        return $Template->run($View, $Vars);
    }
}
?>
