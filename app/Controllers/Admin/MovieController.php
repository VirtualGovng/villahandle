<?php

namespace App\Controllers\Admin;

use App\Core\Registry;
use App\Models\Movie;
use App\Models\User;

class MovieController
{
    /**
     * Handles the storage of an uploaded file.
     * @param array $file The $_FILES array for the uploaded file.
     * @return string|null The web-accessible path to the stored file or null on failure.
     */
    private function handleFileUpload(array $file): ?string
    {
        if (!isset($file['error']) || is_array($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = ROOT_PATH . '/public/storage/uploads/videos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $fileName = uniqid('movie_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return '/public/storage/uploads/videos/' . $fileName;
        }

        error_log("Failed to move uploaded file to: {$targetPath}");
        return null;
    }

    /**
     * Display a paginated list of all movies in the admin panel.
     */
    public function index()
    {
        $movieModel = new Movie();
        $page = $_GET['page'] ?? 1;
        $paginationData = $movieModel->adminGetAllPaginated((int)$page);

        $data = [
            'title' => 'Manage Movies',
            'user' => Registry::get('user'),
            'movies' => $paginationData['movies'],
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $paginationData['currentPage'],
        ];
        return view('admin.pages.movies.index', $data, 'admin.layouts.admin_layout');
    }

    /**
     * Show the form for creating a new movie.
     */
    public function create()
    {
        $data = [
            'title' => 'Add New Movie',
            'user' => Registry::get('user'),
            'creators' => (new User())->getAllCreators(), // Fetch creators for the dropdown
        ];
        return view('admin.pages.movies.create', $data, 'admin.layouts.admin_layout');
    }

    /**
     * Store a newly created movie in the database.
     */
    public function store()
    {
        $videoPath = $this->handleFileUpload($_FILES['video_file'] ?? []);

        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'release_date' => !empty($_POST['release_date']) ? $_POST['release_date'] : null,
            'poster_path' => $_POST['poster_path'],
            'video_path' => $videoPath,
            'genre' => $_POST['genre'],
            'status' => $_POST['status'],
            'is_premium' => $_POST['is_premium'] ?? 0,
            'uploaded_by_user_id' => !empty($_POST['uploaded_by_user_id']) ? $_POST['uploaded_by_user_id'] : null,
        ];
        
        $movieModel = new Movie();
        if ($movieModel->create($data)) {
            $_SESSION['success_message'] = 'Movie created successfully.';
            redirect('/admin/movies');
        } else {
            $_SESSION['error_message'] = 'Failed to create movie.';
            redirect('/admin/movies/create');
        }
    }

    /**
     * Show the form for editing a specific movie.
     */
    public function edit(string $id)
    {
        $movieModel = new Movie();
        $movieToEdit = $movieModel->adminFindById((int)$id);

        if (!$movieToEdit) {
            redirect('/admin/movies');
        }

        $data = [
            'title' => 'Edit Movie: ' . $movieToEdit['title'],
            'user' => Registry::get('user'),
            'movie' => $movieToEdit,
            'creators' => (new User())->getAllCreators(), // Fetch creators for the dropdown
        ];
        return view('admin.pages.movies.edit', $data, 'admin.layouts.admin_layout');
    }
    
    /**
     * Update a specific movie in the database.
     */
    public function update(string $id)
    {
        $movieModel = new Movie();
        $currentMovie = $movieModel->adminFindById((int)$id);
        
        $videoPath = $currentMovie['video_path'];
        if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
            $newVideoPath = $this->handleFileUpload($_FILES['video_file']);
            if ($newVideoPath) {
                if ($videoPath && file_exists(ROOT_PATH . $videoPath)) {
                    unlink(ROOT_PATH . $videoPath);
                }
                $videoPath = $newVideoPath;
            }
        }
        
        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'release_date' => !empty($_POST['release_date']) ? $_POST['release_date'] : null,
            'poster_path' => $_POST['poster_path'],
            'video_path' => $videoPath,
            'genre' => $_POST['genre'],
            'status' => $_POST['status'],
            'is_premium' => $_POST['is_premium'] ?? 0,
            'uploaded_by_user_id' => !empty($_POST['uploaded_by_user_id']) ? $_POST['uploaded_by_user_id'] : null,
        ];
        
        if ($movieModel->update((int)$id, $data)) {
            $_SESSION['success_message'] = 'Movie updated successfully.';
        } else {
            $_SESSION['error_message'] = 'Failed to update movie.';
        }
        redirect('/admin/movies');
    }

    /**
     * Delete a specific movie.
     */
    public function destroy(string $id)
    {
        $movieModel = new Movie();
        $movieToDelete = $movieModel->adminFindById((int)$id);

        if ($movieToDelete && $movieToDelete['video_path']) {
             if (file_exists(ROOT_PATH . $movieToDelete['video_path'])) {
                unlink(ROOT_PATH . $movieToDelete['video_path']);
            }
        }

        if ($movieModel->delete((int)$id)) {
            $_SESSION['success_message'] = 'Movie deleted successfully.';
        } else {
            $_SESSION['error_message'] = 'Failed to delete movie.';
        }
        redirect('/admin/movies');
    }
}