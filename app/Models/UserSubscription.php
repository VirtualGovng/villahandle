<?php

namespace App\Models;

use App\Core\Database;

class UserSubscription
{
    protected \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Fetches a paginated list of all user subscriptions for the admin panel.
     */
    public function adminGetAllPaginated(int $page = 1, int $perPage = 15): array
    {
        try {
            $countStmt = $this->db->query("SELECT COUNT(id) FROM user_subscriptions");
            $total = (int) $countStmt->fetchColumn();
            $totalPages = ceil($total / $perPage);
            $offset = ($page - 1) * $perPage;

            $sql = "SELECT us.*, u.username, sp.name as plan_name
                    FROM user_subscriptions us
                    JOIN users u ON us.user_id = u.id
                    JOIN subscription_plans sp ON us.plan_id = sp.id
                    ORDER BY us.start_date DESC
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':limit', $perPage, \PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();

            return ['subscriptions' => $stmt->fetchAll(), 'totalPages' => $totalPages, 'currentPage' => $page];
        } catch (\PDOException $e) {
            error_log("UserSubscription Model Error (adminGetAllPaginated): " . $e->getMessage());
            return ['subscriptions' => [], 'totalPages' => 0, 'currentPage' => 1];
        }
    }
    
    /**
     * Counts all active subscriptions for the dashboard.
     */
    public function countAllActive(): int
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(id) FROM user_subscriptions WHERE status = 'active' AND end_date > NOW()");
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("UserSubscription Model Error (countAllActive): " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Finds a user's most recent active subscription.
     */
    public function findActiveByUserId(int $userId)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT 
                    us.id, us.start_date, us.end_date, us.status, us.payment_gateway,
                    sp.name as plan_name, sp.description as plan_description
                 FROM user_subscriptions us
                 JOIN subscription_plans sp ON us.plan_id = sp.id
                 WHERE us.user_id = :user_id 
                 AND us.status = 'active' 
                 AND us.end_date > NOW()
                 ORDER BY us.end_date DESC
                 LIMIT 1"
            );
            $stmt->bindParam(':user_id', $userId, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("UserSubscription Model Error (findActiveByUserId): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Checks if a user has a currently active subscription.
     */
    public function hasActiveSubscription(int $userId): bool
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT 1 FROM user_subscriptions 
                 WHERE user_id = :user_id 
                 AND status = 'active' 
                 AND end_date > NOW()
                 LIMIT 1"
            );
            $stmt->bindParam(':user_id', $userId, \PDO::PARAM_INT);
            $stmt->execute();
            return (bool) $stmt->fetchColumn(); 
        } catch (\PDOException $e) {
            error_log("UserSubscription Model Error (hasActiveSubscription): " . $e->getMessage());
            return false;
        }
    }
}