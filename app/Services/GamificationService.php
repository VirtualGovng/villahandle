<?php

namespace App\Services;

use App\Core\Database;

class GamificationService
{
    protected \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Awards a badge to a user if they don't already have it.
     *
     * @param int $userId
     * @param int $badgeId
     * @return bool True if the badge was newly awarded, false otherwise.
     */
    private function awardBadge(int $userId, int $badgeId): bool
    {
        try {
            // IGNORE ensures that if the user_badge_unique constraint is violated, it just fails silently.
            $stmt = $this->db->prepare(
                "INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (:user_id, :badge_id)"
            );
            $stmt->execute([':user_id' => $userId, ':badge_id' => $badgeId]);
            
            // rowCount() will be 1 if a new badge was inserted, 0 if it was ignored.
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("Gamification Service Error (awardBadge): " . $e->getMessage());
            return false;
        }
    }

    /**
     * This is the main event handler. Call this after a user performs an action.
     * It checks all relevant conditions and awards badges accordingly.
     *
     * @param string $event The event that occurred (e.g., 'review_posted').
     * @param int $userId The ID of the user who triggered the event.
     */
    public function triggerEvent(string $event, int $userId): void
    {
        if ($event === 'review_posted') {
            $this->checkReviewBadges($userId);
        }
        // Add other events here, e.g., 'user_registered', 'movie_watched'
    }

    /**
     * Checks and awards badges related to posting reviews.
     */
    private function checkReviewBadges(int $userId): void
    {
        try {
            // Get the total number of reviews this user has posted.
            $stmt = $this->db->prepare("SELECT COUNT(id) FROM reviews WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $userId]);
            $reviewCount = (int) $stmt->fetchColumn();

            // Check for "First Review" badge (ID 1)
            if ($reviewCount >= 1) {
                if ($this->awardBadge($userId, 1)) {
                    // A new badge was awarded, set a session message!
                    $_SESSION['success_message'] = "Congratulations! You've earned the 'First Review' badge!";
                }
            }
            
            // Check for "Movie Buff" badge (ID 2)
            if ($reviewCount >= 5) {
                if ($this->awardBadge($userId, 2)) {
                    $_SESSION['success_message'] = "Wow! You've earned the 'Movie Buff' badge!";
                }
            }

            // Check for "Super Critic" badge (ID 3)
            if ($reviewCount >= 10) {
                 if ($this->awardBadge($userId, 3)) {
                    $_SESSION['success_message'] = "Incredible! You are now a 'Super Critic'!";
                }
            }
        } catch (\PDOException $e) {
            error_log("Gamification Service Error (checkReviewBadges): " . $e->getMessage());
        }
    }
}