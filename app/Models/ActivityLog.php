<?php

namespace App\Models;

use App\Core\Database;

class ActivityLog
{
    protected \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Creates a new activity log entry.
     */
    public function create(string $action, string $description, ?int $userId = null): bool
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO activity_log (user_id, action, description) VALUES (:user_id, :action, :description)"
            );
            return $stmt->execute([
                ':user_id' => $userId,
                ':action' => $action,
                ':description' => $description,
            ]);
        } catch (\PDOException $e) {
            error_log("ActivityLog Model Error (create): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetches the most recent activity logs for the dashboard.
     */
    public function getRecent(int $limit = 5): array
    {
        try {
            $stmt = $this->db->query(
                "SELECT al.*, u.username 
                 FROM activity_log al
                 LEFT JOIN users u ON al.user_id = u.id
                 ORDER BY al.created_at DESC
                 LIMIT {$limit}"
            );
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("ActivityLog Model Error (getRecent): " . $e->getMessage());
            return [];
        }
    }
}