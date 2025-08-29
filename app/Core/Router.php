<?php

namespace App\Core;

class Router
{
    protected static array $routes = [];

    protected static function add(string $method, string $uri, array $action, ?string $middleware = null): void
    {
        self::$routes[] = compact('method', 'uri', 'action', 'middleware');
    }

    public static function get(string $uri, array $action, ?string $middleware = null): void
    {
        self::add('GET', '/' . trim($uri, '/'), $action, $middleware);
    }

    public static function post(string $uri, array $action, ?string $middleware = null): void
    {
        self::add('POST', '/' . trim($uri, '/'), $action, $middleware);
    }

    public static function direct(string $uri, string $requestMethod)
    {
        foreach (self::$routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $route['uri']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                Middleware::resolve($route['middleware']);
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return self::callAction($route['action'][0], $route['action'][1], $params);
            }
        }
        
        self::handleNotFound();
    }
    
    protected static function callAction($controller, $method, $params = [])
    {
        if (!class_exists($controller)) {
            self::triggerError("Controller class {$controller} does not exist.");
            return;
        }
        $controllerInstance = new $controller();
        if (!method_exists($controllerInstance, $method)) {
            self::triggerError("Method {$method} does not exist on controller {$controller}.");
            return;
        }
        return call_user_func_array([$controllerInstance, $method], $params);
    }
    
    public static function handleNotFound()
    {
        http_response_code(404);
        view('pages.errors.404');
        exit();
    }

    protected static function triggerError($message)
    {
        error_log($message);
        if (env('APP_DEBUG', false)) {
            echo "<h1>Routing Error</h1><p>{$message}</p>";
        } else {
            self::handleNotFound();
        }
        exit();
    }
}