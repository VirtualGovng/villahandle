<?php
namespace App\Core;

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