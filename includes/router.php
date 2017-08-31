<?php
if (isset($RouteCollectors))
{
    $dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) use ($RouteCollectors) {
        foreach ($RouteCollectors as $v) { new $v($r); }
    });

    // Fetch method and URI from somewhere
    $httpMethod = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];

    // Strip query string (?foo=bar) and decode URI
    if (false !== $pos = strpos($uri, '?')) {
        $uri = substr($uri, 0, $pos);
    }
    $uri = rawurldecode($uri).'/';
    $uri = str_replace('//', '/', $uri);

    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            // ... 404 Not Found
            $handler = '\App\Controllers\Controller@Page';
            $vars = ['NotFound' => 1];
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            // ... 405 Method Not Allowed
            break;
        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];
            // ... call $handler with $vars
            break;
    }

    list($ControllerName, $Method) = explode('@', $handler);
    $Controller = new $ControllerName ($SESSION, $Method);
    $Controller->$Method($vars);
}
?>