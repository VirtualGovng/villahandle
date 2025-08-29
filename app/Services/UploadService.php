<?php

namespace App\Services;

class UploadService
{
    /**
     * Handles the storage of an uploaded video file.
     *
     * @param array $file The $_FILES array for the uploaded file.
     * @return string|null The web-accessible path to the stored file or null on failure.
     */
    public function handleVideoUpload(array $file): ?string
    {
        if (!isset($file['error']) || is_array($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null; // No file or upload error
        }

        // Basic security checks
        $allowedTypes = ['video/mp4', 'video/webm', 'video/ogg'];
        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['error_message'] = 'Invalid file type. Only MP4, WebM, and OGG are allowed.';
            return null;
        }

        // Check file size (e.g., 500MB limit)
        if ($file['size'] > 500 * 1024 * 1024) {
            $_SESSION['error_message'] = 'File is too large. Maximum size is 500MB.';
            return null;
        }

        $uploadDir = ROOT_PATH . '/public/storage/uploads/creator_videos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $fileName = uniqid('movie_', true) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Return the web-accessible path
            return '/public/storage/uploads/creator_videos/' . $fileName;
        }

        error_log("Failed to move uploaded creator file to: {$targetPath}");
        $_SESSION['error_message'] = 'Server error: Could not save the uploaded file.';
        return null;
    }
}