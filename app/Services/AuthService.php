<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSubscription; // Import the new model

class AuthService
{
    /**
     * Attempt to log in a user with the given credentials.
     *
     * @param string $email
     * @param string $password
     * @return bool True on success, false on failure.
     */
    public static function attempt(string $email, string $password): bool
    {
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        // 1. Check if user exists and their password is correct.
        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        // 2. Check if the user account is active.
        if ($user['status'] !== 'active') {
            $_SESSION['error_message'] = 'Your account is not active. Please contact support.';
            return false;
        }

        // 3. Regenerate session ID to prevent session fixation attacks.
        session_regenerate_id(true);

        // 4. Store user data in the session.
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        return true;
    }

    /**
     * Check if a user is currently logged in.
     *
     * @return bool
     */
    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    /**
     * Get the currently authenticated user's data.
     *
     * @return array|null The user data array or null if not logged in.
     */
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
    
    /**
     * Get the currently authenticated user's ID.
     *
     * @return int|null The user ID or null if not logged in.
     */
    public static function id(): ?int
    {
        return $_SESSION['user']['id'] ?? null;
    }

    /**
     * Check if the logged-in user has a specific role.
     *
     * @param string $role The role to check (e.g., 'admin', 'creator').
     * @return bool
     */
    public static function hasRole(string $role): bool
    {
        if (!self::check()) {
            return false;
        }
        return self::user()['role'] === $role;
    }

    /**
     * Log the user out by destroying the session.
     */
    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
    }

    /**
     * A new, convenient helper to check if the currently logged-in user
     * has an active subscription.
     *
     * @return bool
     */
    public static function userHasActiveSubscription(): bool
    {
        // If the user is not even logged in, they can't have a subscription.
        if (!self::check()) {
            return false;
        }
        
        // Check the database for an active subscription for the current user's ID.
        $subscriptionModel = new UserSubscription();
        return $subscriptionModel->hasActiveSubscription(self::id());
    }
}