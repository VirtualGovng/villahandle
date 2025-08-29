<?php

namespace App\Controllers\Admin;

use App\Core\Registry;
use App\Models\UserSubscription;

class SubscriptionController
{
    public function index()
    {
        $subscriptionModel = new UserSubscription();
        $page = $_GET['page'] ?? 1;
        $paginationData = $subscriptionModel->adminGetAllPaginated((int)$page);

        $data = [
            'title' => 'Manage Subscriptions',
            'user' => Registry::get('user'),
            'subscriptions' => $paginationData['subscriptions'],
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $paginationData['currentPage'],
        ];
        return view('admin.pages.subscriptions.index', $data, 'admin.layouts.admin_layout');
    }
}