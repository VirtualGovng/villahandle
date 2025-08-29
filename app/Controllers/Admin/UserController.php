<?php

namespace App\Controllers\Admin;

use App\Core\Registry;
use App\Models\User;

class UserController
{
    public function index()
    {
        $userModel = new User();
        $page = $_GET['page'] ?? 1;
        $paginationData = $userModel->adminGetAllPaginated((int)$page);

        $data = [
            'title' => 'Manage Users',
            'user' => Registry::get('user'),
            'users' => $paginationData['users'],
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $paginationData['currentPage'],
        ];
        return view('admin.pages.users.index', $data, 'admin.layouts.admin_layout');
    }

    public function edit(string $id)
    {
        $userModel = new User();
        $userToEdit = $userModel->findById((int)$id);

        if (!$userToEdit) {
            // Handle user not found
            redirect('/admin/users');
        }

        $data = [
            'title' => 'Edit User: ' . $userToEdit['username'],
            'user' => Registry::get('user'),
            'userToEdit' => $userToEdit,
        ];
        return view('admin.pages.users.edit', $data, 'admin.layouts.admin_layout');
    }

    public function update(string $id)
    {
        // Simple validation
        $data = [
            'username' => trim($_POST['username']),
            'email' => filter_var($_POST['email'], FILTER_VALIDATE_EMAIL),
            'first_name' => trim($_POST['first_name']),
            'last_name' => trim($_POST['last_name']),
            'role' => $_POST['role'],
            'status' => $_POST['status'],
        ];

        // Add more robust validation here in a real app
        
        $userModel = new User();
        if ($userModel->update((int)$id, $data)) {
            $_SESSION['success_message'] = 'User updated successfully.';
        } else {
            $_SESSION['error_message'] = 'Failed to update user.';
        }
        redirect('/admin/users');
    }

    public function destroy(string $id)
    {
        $userModel = new User();
        // Add check to prevent deleting self or super admin
        if ($userModel->delete((int)$id)) {
            $_SESSION['success_message'] = 'User deleted successfully.';
        } else {
            $_SESSION['error_message'] = 'Failed to delete user.';
        }
        redirect('/admin/users');
    }
}