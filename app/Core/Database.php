<?php

namespace App\Core;

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