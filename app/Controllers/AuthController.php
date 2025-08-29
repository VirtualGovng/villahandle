<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\AuthService;
use App\Models\ActivityLog; // Import the ActivityLog model

class AuthController
{
    /**
     * Display the login form.
     */
    public function showLoginForm()
    {
        if (AuthService::check()) {
            header('Location: /');
            exit();
        }
        
        $data = ['title' => 'Sign In - VillaStudio'];
        return view('pages.login', $data);
    }

    /**
     * Handle the login form submission.
     */
    public function login()
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (!$email || empty($password)) {
            $_SESSION['error_message'] = 'Invalid email or password.';
            header('Location: /login');
            exit();
        }

        if (AuthService::attempt($email, $password)) {
            $_SESSION['success_message'] = 'Welcome back!';
            header('Location: /');
            exit();
        } else {
            if (!isset($_SESSION['error_message'])) {
                $_SESSION['error_message'] = 'Invalid credentials. Please try again.';
            }
            header('Location: /login');
            exit();
        }
    }

    /**
     * Display the registration form.
     */
    public function showRegistrationForm()
    {
        if (AuthService::check()) {
            header('Location: /');
            exit();
        }
        
        $data = ['title' => 'Create Account - VillaStudio'];
        return view('pages.register', $data);
    }

    /**
     * Handle the registration form submission.
     */
    public function register()
    {
        $username = trim($_POST['username'] ?? '');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if (empty($username) || !$email || empty($password) || $password !== $passwordConfirm) {
            $_SESSION['error_message'] = 'Please fill in all fields correctly. Passwords must match.';
            header('Location: /register');
            exit();
        }
        
        if (strlen($password) < 8) {
             $_SESSION['error_message'] = 'Password must be at least 8 characters long.';
            header('Location: /register');
            exit();
        }

        $userModel = new User();
        
        if ($userModel->findByEmail($email)) {
            $_SESSION['error_message'] = 'An account with this email already exists.';
            header('Location: /register');
            exit();
        }

        $newUserId = $userModel->create([
            'username' => $username,
            'email' => $email,
            'password' => $password
        ]);

        if ($newUserId) {
            // Automatically log the user in after successful registration
            AuthService::attempt($email, $password);
            $_SESSION['success_message'] = 'Your account has been created successfully!';
            
            // Log the registration activity
            (new ActivityLog())->create('user.register', "New user '{$username}' registered.", (int)$newUserId);
            
            header('Location: /');
            exit();
        } else {
            $_SESSION['error_message'] = 'Could not create your account due to a server error. Please try again.';
            header('Location: /register');
            exit();
        }
    }

    /**
     * Log the user out and redirect to the homepage.
     */
    public function logout()
    {
        AuthService::logout();
        $_SESSION['success_message'] = 'You have been logged out.';
        header('Location: /');
        exit();
    }
}