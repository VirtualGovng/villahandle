<?php

namespace App\Models;

use App\Core\Database;

class Badge
{
    protected \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Fetches all badges a specific user has earned.
     *
     * @param int $userId
     * @return array
     */
    public function getBadgesByUserId(int $userId): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT b.name, b.description, b.icon_class, b.color_class
                 FROM user_badges ub
                 JOIN badges b ON ub.badge_id = b.id
                 WHERE ub.user_id = :user_id
                 ORDER BY ub.earned_at DESC"
            );
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Badge Model Error (getBadgesByUserId): " . $e->getMessage());
            return [];
        }
    }
}