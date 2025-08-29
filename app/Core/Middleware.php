<?php

namespace App\Core;

use App\Services\AuthService;

class Middleware
{
    const MAP = [
        'auth' => AuthMiddleware::class,
        'guest' => GuestMiddleware::class,
        'admin' => AdminMiddleware::class,
    ];

    public static function resolve(?string $key): void
    {
        if (!$key) return;
        $middlewareClass = static::MAP[$key] ?? false;
        if (!$middlewareClass) throw new \Exception("No middleware found for key '{$key}'.");
        (new $middlewareClass)->handle();
    }
}

// --- Individual Middleware Classes ---

class AuthMiddleware
{
    public function handle()
    {
        if (!AuthService::check()) {
            $_SESSION['error_message'] = 'You must be logged in to view that page.';
            redirect('/login');
        }
        Registry::set('user', AuthService::user());
    }
}

class GuestMiddleware
{
    public function handle()
    {
        if (AuthService::check()) {
            redirect('/');
        }
    }
}

class AdminMiddleware
{
    public function handle()
    {
        // --- THIS IS THE CRITICAL FIX ---
        // Step 1: Check if the user is a guest. If so, they MUST be redirected to login.
        // This is the check that was failing.
        if (!AuthService::check()) {
            $_SESSION['error_message'] = 'You must be an administrator to access this area.';
            redirect('/login');
        }

        // If they are logged in, store their data in the registry for the next check.
        Registry::set('user', AuthService::user());

        // Step 2: Now that we know they are logged in, check if they have the 'admin' role.
        if (!AuthService::hasRole('admin')) {
            // They are a regular user, not an admin. Show a 403 Forbidden error.
            http_response_code(403);
            view('pages.errors.403');
            exit();
        }
        // If both checks pass, they are an admin and can proceed.
    }
}