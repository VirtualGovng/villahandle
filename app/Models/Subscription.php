<?php

namespace App\Models;

use App\Core\Database;

class Subscription
{
    protected \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get details of a specific subscription plan by its ID.
     */
    public function getPlanById(int $planId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM subscription_plans WHERE id = :id AND status = 'active'");
        $stmt->execute([':id' => $planId]);
        $plan = $stmt->fetch();
        return $plan ?: null;
    }
    
    /**
     * Get all active subscription plans.
     */
    public function getActivePlans(): array
    {
        $stmt = $this->db->query("SELECT * FROM subscription_plans WHERE status = 'active' ORDER BY price ASC");
        return $stmt->fetchAll();
    }

    /**
     * Create or update a user's subscription.
     */
    public function createOrUpdate(int $userId, int $planId, int $durationDays, string $gateway, string $gatewaySubId): bool
    {
        try {
            // In a real app, you'd check for an existing active subscription and decide whether to update or overwrite.
            // For simplicity, we'll insert a new one.
            $startDate = date('Y-m-d H:i:s');
            $endDate = date('Y-m-d H:i:s', strtotime("+{$durationDays} days"));

            $stmt = $this->db->prepare(
                "INSERT INTO user_subscriptions (user_id, plan_id, start_date, end_date, status, payment_gateway, gateway_subscription_id)
                 VALUES (:user_id, :plan_id, :start_date, :end_date, 'active', :gateway, :gateway_sub_id)"
            );

            return $stmt->execute([
                ':user_id' => $userId,
                ':plan_id' => $planId,
                ':start_date' => $startDate,
                ':end_date' => $endDate,
                ':gateway' => $gateway,
                ':gateway_sub_id' => $gatewaySubId,
            ]);
        } catch (\PDOException $e) {
            error_log("Subscription Model Error: " . $e->getMessage());
            return false;
        }
    }
}