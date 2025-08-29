<?php

namespace App\Core;

/**
 * A simple static class to hold global application data, like the authenticated user.
 * This acts as a service container or registry.
 */
class Registry
{
    private static array $data = [];

    /**
     * Bind a value into the registry.
     *
     * @param string $key The key to bind.
     * @param mixed $value The value to store.
     */
    public static function set(string $key, $value): void
    {
        self::$data[$key] = $value;
    }

    /**
     * Get a value from the registry.
     *
     * @param string $key The key to retrieve.
     * @return mixed|null The stored value or null if not found.
     */
    public static function get(string $key)
    {
        return self::$data[$key] ?? null;
    }

    /**
     * Check if a key exists in the registry.
     *
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset(self::$data[$key]);
    }
}