<?php namespace App\Modules\Users;

use \FastRoute\RouteCollector;

// Set the routes
class Routes
{
    public function __construct (RouteCollector $R)
    {
        $R->addGroup('/Admin', function (RouteCollector $R) {
            $R->get('/', '\App\Modules\Users\Controllers\Admin@index');
            $R->addRoute(['GET', 'POST'], '/Login/', '\App\Modules\Users\Controllers\Admin@Login');
            $R->get('/page/{id:\d+}/', '\App\Modules\Users\Controllers\Admin@Page');
            $R->addRoute(['GET', 'POST'], '/ForgotPassword/', '\App\Modules\Users\Controllers\Admin@ForgotPassword');
        });
    }
}
?>