<?php

namespace App\Controllers;

use App\Models\Movie;
use App\Services\AuthService;
use App\Services\UploadService;
use App\Core\Middleware;

// This middleware will protect the entire creator portal
class CreatorMiddleware
{
    public function handle()
    {
        if (!AuthService::check()) {
            $_SESSION['error_message'] = 'You must be logged in to access the Creator Portal.';
            redirect('/login');
        }
        if (!AuthService::hasRole('creator')) {
            http_response_code(403);
            view('pages.errors.403');
            exit();
        }
    }
}

class CreatorController
{
    public function __construct()
    {
        // Run the creator middleware for all methods in this controller, ensuring security.
        (new CreatorMiddleware)->handle();
    }
    
    /**
     * Show the creator's main dashboard with a list of their uploaded movies.
     */
    public function dashboard()
    {
        $movieModel = new Movie();
        $myMovies = $movieModel->getMoviesByCreatorId(AuthService::id());

        $data = [
            'title' => 'Creator Dashboard - VillaStudio',
            'myMovies' => $myMovies,
        ];
        return view('pages.creator_dashboard', $data);
    }

    /**
     * Show the form for uploading a new movie.
     */
    public function showUploadForm()
    {
        $data = [
            'title' => 'Upload Your Film - VillaStudio',
        ];
        return view('pages.upload_movie', $data);
    }

    /**
     * Handle the submission of the movie upload form.
     */
    public function store()
    {
        $uploadService = new UploadService();
        $videoPath = $uploadService->handleVideoUpload($_FILES['video_file'] ?? []);

        if (!$videoPath) {
            // The UploadService sets the specific error message in the session.
            redirect('/creator/movies/upload');
        }

        $data = [
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'release_date' => !empty($_POST['release_date']) ? $_POST['release_date'] : null,
            'poster_path' => trim($_POST['poster_path']),
            'video_path' => $videoPath,
            'genre' => trim($_POST['genre']),
            'status' => 'pending', // Creator uploads are 'pending' by default
            'is_premium' => $_POST['is_premium'] ?? 0,
            'uploaded_by_user_id' => AuthService::id(),
        ];

        $movieModel = new Movie();
        if ($movieModel->create($data)) {
            $_SESSION['success_message'] = 'Your film has been submitted for review successfully!';
            redirect('/creator/dashboard');
        } else {
            $_SESSION['error_message'] = 'Failed to save your film details to the database.';
            redirect('/creator/movies/upload');
        }
    }

    /**
     * Show the form for a creator to edit their own movie submission.
     */
    public function editMovie(string $id)
    {
        $movieModel = new Movie();
        $movie = $movieModel->adminFindById((int)$id);

        // SECURITY CHECK: Ensure the movie exists and belongs to the current creator.
        if (!$movie || $movie['uploaded_by_user_id'] !== AuthService::id()) {
            $_SESSION['error_message'] = 'You are not authorized to edit this film.';
            redirect('/creator/dashboard');
        }

        // Creators should only be able to edit films that are 'pending' or 'draft'.
        if (!in_array($movie['status'], ['pending', 'draft', 'rejected'])) {
            $_SESSION['error_message'] = 'This film has already been published and cannot be edited.';
            redirect('/creator/dashboard');
        }

        $data = [
            'title' => 'Edit Your Film: ' . htmlspecialchars($movie['title']),
            'movie' => $movie,
        ];
        return view('pages.edit_movie', $data);
    }
    
    /**
     * Update a creator's movie submission in the database.
     */
    public function updateMovie(string $id)
    {
        $movieModel = new Movie();
        $movie = $movieModel->adminFindById((int)$id);

        // Re-run security checks before updating
        if (!$movie || $movie['uploaded_by_user_id'] !== AuthService::id() || !in_array($movie['status'], ['pending', 'draft', 'rejected'])) {
             $_SESSION['error_message'] = 'You are not authorized to perform this action.';
             redirect('/creator/dashboard');
        }
        
        $data = [
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'release_date' => !empty($_POST['release_date']) ? $_POST['release_date'] : null,
            'poster_path' => trim($_POST['poster_path']),
            'video_path' => $movie['video_path'], // Keep existing video path, editing metadata only
            'genre' => trim($_POST['genre']),
            'status' => 'pending', // Re-set to pending for re-review after any edit
            'is_premium' => $_POST['is_premium'] ?? 0,
        ];

        if ($movieModel->update((int)$id, $data)) {
            $_SESSION['success_message'] = 'Your film has been updated and re-submitted for review.';
        } else {
            $_SESSION['error_message'] = 'Failed to update your film details.';
        }
        redirect('/creator/dashboard');
    }
}