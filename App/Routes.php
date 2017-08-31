<?php namespace App;

use \FastRoute\RouteCollector;

// Set the routes
class Routes
{
    public function __construct (RouteCollector $R)
    {
        $R->addRoute(['GET', 'POST'], '/', '\App\Controllers\Controller@index');
        $R->addRoute(['GET', 'POST'], '/{slug}/', '\App\Controllers\Controller@Page');
    }
}
?>