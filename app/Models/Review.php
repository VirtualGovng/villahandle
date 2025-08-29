<?php

namespace App\Models;

use App\Core\Database;

class Review
{
    protected \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Fetches all reviews for a given movie ID.
     */
    public function getByMovieId(int $movieId): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT r.*, u.username 
                 FROM reviews r
                 JOIN users u ON r.user_id = u.id
                 WHERE r.movie_id = :movie_id
                 ORDER BY r.created_at DESC"
            );
            $stmt->execute([':movie_id' => $movieId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Review Model Error (getByMovieId): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Creates a new review for either a movie OR a series.
     */
    public function create(int $userId, int $contentId, string $contentType, int $rating, string $comment): bool
    {
        try {
            // Determine which column to populate: movie_id or series_id
            $contentColumn = ($contentType === 'movie') ? 'movie_id' : 'series_id';

            $stmt = $this->db->prepare(
                "INSERT INTO reviews (user_id, {$contentColumn}, rating, comment) 
                 VALUES (:user_id, :content_id, :rating, :comment)"
            );
            
            return $stmt->execute([
                ':user_id' => $userId,
                ':content_id' => $contentId,
                ':rating' => $rating,
                ':comment' => $comment,
            ]);
        } catch (\PDOException $e) {
            error_log("Review Model Error (create): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Checks if a user has already reviewed a piece of content (movie or series).
     */
    public function hasUserReviewed(int $userId, int $contentId, string $contentType): bool
    {
        try {
            $contentColumn = ($contentType === 'movie') ? 'movie_id' : 'series_id';

            $stmt = $this->db->prepare(
                "SELECT COUNT(id) FROM reviews WHERE user_id = :user_id AND {$contentColumn} = :content_id"
            );
            $stmt->execute([':user_id' => $userId, ':content_id' => $contentId]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            error_log("Review Model Error (hasUserReviewed): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Fetches the most recent reviews across both movies and series for the Community page.
     */
    public function getRecent(int $limit = 20): array
    {
        try {
            $sql = "SELECT r.*, u.username, 
                           COALESCE(m.title, s.title) as content_title,
                           COALESCE(m.slug, s.slug) as content_slug,
                           IF(r.movie_id IS NOT NULL, 'movies', 'series') as content_type
                    FROM reviews r
                    JOIN users u ON r.user_id = u.id
                    LEFT JOIN movies m ON r.movie_id = m.id
                    LEFT JOIN series s ON r.series_id = s.id
                    WHERE COALESCE(m.title, s.title) IS NOT NULL
                    ORDER BY r.created_at DESC
                    LIMIT :limit";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Review Model Error (getRecent): " . $e->getMessage());
            return [];
        }
    }
}