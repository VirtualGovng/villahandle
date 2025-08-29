<?php

namespace App\Controllers\Admin;

use App\Core\Registry;

class SettingsController
{
    public function index()
    {
        $data = [
            'title' => 'Site Settings',
            'user' => Registry::get('user'),
        ];
        return view('admin.pages.settings.index', $data, 'admin.layouts.admin_layout');
    }
}