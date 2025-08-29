<?php

namespace App\Controllers;

use App\Models\Review;
use App\Services\AuthService;
use App\Services\GamificationService; // <-- Import the new service

class ReviewController
{
    public function store()
    {
        $movieId = filter_input(INPUT_POST, 'movie_id', FILTER_VALIDATE_INT);
        $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
        $comment = trim(htmlspecialchars($_POST['comment'] ?? ''));
        $movieSlug = trim($_POST['movie_slug'] ?? '');

        $redirectPath = $movieSlug ? "/movies/{$movieSlug}" : "/";

        if (!$movieId || !$rating || empty($comment)) {
            $_SESSION['error_message'] = 'Please provide a rating and a comment.';
            redirect($redirectPath);
        }

        $userId = AuthService::id();
        $reviewModel = new Review();

        if ($reviewModel->hasUserReviewed($userId, $movieId)) {
            $_SESSION['error_message'] = 'You have already reviewed this movie.';
            redirect($redirectPath);
        }

        if ($reviewModel->create($userId, $movieId, $rating, $comment)) {
            // --- THIS IS THE FIX ---
            // After the review is created, trigger the gamification service.
            $gamificationService = new GamificationService();
            $gamificationService->triggerEvent('review_posted', $userId);
            
            // The gamification service sets its own success message if a badge is earned.
            // Only set a generic one if no badge was awarded.
            if (!isset($_SESSION['success_message'])) {
                $_SESSION['success_message'] = 'Your review has been posted successfully!';
            }
        } else {
            $_SESSION['error_message'] = 'There was an error posting your review.';
        }
        
        redirect($redirectPath);
    }
}