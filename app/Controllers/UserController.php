<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Badge; // Import the new Badge model

class UserController
{
    /**
     * Show the user's main profile page, now including their earned badges.
     */
    public function profile()
    {
        // Middleware-like check for authentication
        if (!AuthService::check()) {
            $_SESSION['error_message'] = 'You must be logged in to view this page.';
            redirect('/login');
        }

        $userModel = new User();
        $badgeModel = new Badge(); // Instantiate the Badge model

        // Fetch the current user's data
        $user = $userModel->findById(AuthService::id());

        // If for some reason the user in the session doesn't exist in the DB, log them out.
        if (!$user) {
            AuthService::logout();
            $_SESSION['error_message'] = 'Could not find your user data. Please log in again.';
            redirect('/login');
        }

        // Fetch the user's earned badges
        $badges = $badgeModel->getBadgesByUserId(AuthService::id());

        // Prepare all data for the view
        $data = [
            'title' => 'My Profile - VillaStudio',
            'user' => $user,
            'badges' => $badges // Pass the badges to the view
        ];
        
        return view('pages.profile', $data);
    }

    /**
     * Show the user's subscription management page.
     */
    public function subscription()
    {
        // Middleware-like check for authentication
        if (!AuthService::check()) {
            $_SESSION['error_message'] = 'You must be logged in to view this page.';
            redirect('/login');
        }

        $subscriptionModel = new UserSubscription();
        $subscription = $subscriptionModel->findActiveByUserId(AuthService::id());

        $data = [
            'title' => 'My Subscription - VillaStudio',
            'subscription' => $subscription
        ];

        return view('pages.profile_subscription', $data);
    }
}