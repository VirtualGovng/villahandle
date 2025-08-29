<?php

namespace App\Controllers\Admin;

use App\Core\Registry;
use App\Models\User;
use App\Models\Movie;
use App\Models\UserSubscription;
use App\Models\Transaction;
use App\Models\ActivityLog;

class DashboardController
{
    /**
     * Show the main admin dashboard with live data.
     * Access is already protected by the 'admin' middleware in routes.php.
     */
    public function index()
    {
        // Fetch REAL data from the respective models
        $userCount = (new User())->countAll();
        $movieCount = (new Movie())->countAll(); // This counts only 'published' movies
        $subscriptionCount = (new UserSubscription())->countAllActive();
        $totalRevenue = (new Transaction())->getTotalRevenue();
        $recentActivity = (new ActivityLog())->getRecent(5);

        // Prepare all data for the view
        $data = [
            'title' => 'Admin Dashboard',
            'user' => Registry::get('user'), // User data from middleware
            'userCount' => $userCount,
            'movieCount' => $movieCount,
            'subscriptionCount' => $subscriptionCount,
            'totalRevenue' => $totalRevenue,
            'recentActivity' => $recentActivity,
        ];
        
        return view('admin.pages.dashboard', $data, 'admin.layouts.admin_layout');
    }
}