<?php

namespace App\Core;

// --- Load Global Helper Functions ---
require APP_PATH . '/helpers.php';

// --- Core Classes ---

class Database
{
    private static ?\PDO $instance = null;
    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): \PDO
    {
        if (self::$instance === null) {
            $config = require CONFIG_PATH . '/database.php';
            $dbConfig = $config['connections'][$config['default']];
            $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
            try {
                self::$instance = new \PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
            } catch (\PDOException $e) {
                error_log("Database Connection Error: " . $e->getMessage());
                if (env('APP_DEBUG', false)) {
                     die("Could not connect to the database. Error: " . $e->getMessage());
                }
                die("Could not connect to the database. Please try again later.");
            }
        }
        return self::$instance;
    }
}

class Router
{
    protected static array $routes = [];

    public static function get(string $uri, array $action): void
    {
        // Ensure URI starts with a slash for consistency
        self::$routes['GET']['/' . trim($uri, '/')] = $action;
    }

    public static function post(string $uri, array $action): void
    {
        // Ensure URI starts with a slash for consistency
        self::$routes['POST']['/' . trim($uri, '/')] = $action;
    }

    public static function direct(string $uri, string $requestMethod)
    {
        // First, check for a direct static match (e.g., '/login', '/register')
        if (array_key_exists($uri, self::$routes[$requestMethod])) {
            $action = self::$routes[$requestMethod][$uri];
            return self::callAction($action[0], $action[1]);
        }

        // If no direct match, check for routes with dynamic parameters
        foreach (self::$routes[$requestMethod] as $route => $action) {
            if (strpos($route, '{') === false) continue;

            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[a-zA-Z0-9_-]+)', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return self::callAction($action[0], $action[1], $params);
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
    
    /**
     * Handles 404 Not Found errors.
     * Changed from protected to public to allow controllers to call it.
     */
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

class Application
{
    public function __construct() { $this->configure(); }
    protected function configure(): void
    {
        $config = require CONFIG_PATH . '/app.php';
        date_default_timezone_set($config['timezone']);
    }
    public function run(): void
    {
        require CONFIG_PATH . '/routes.php';
        $uri = '/' . trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $method = $_SERVER['REQUEST_METHOD'];
        Router::direct($uri, $method);
    }
}