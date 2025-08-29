<?php

namespace App\Models;

use App\Core\Database;

class Transaction
{
    protected \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Create a new pending transaction record.
     */
    public function createPending(int $userId, int $planId, float $amount, string $currency, string $gateway, string $gatewayTxId): ?string
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO transactions (user_id, plan_id, amount, currency, status, payment_gateway, gateway_transaction_id)
                 VALUES (:user_id, :plan_id, :amount, :currency, 'pending', :gateway, :gateway_tx_id)"
            );
            $stmt->execute([
                ':user_id' => $userId,
                ':plan_id' => $planId,
                ':amount' => $amount,
                ':currency' => $currency,
                ':gateway' => $gateway,
                ':gateway_tx_id' => $gatewayTxId
            ]);
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Transaction Model Error (createPending): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update the status of a transaction using its gateway transaction ID/reference.
     */
    public function updateStatus(string $gatewayTxId, string $status): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE transactions SET status = :status WHERE gateway_transaction_id = :gateway_tx_id"
            );
            return $stmt->execute([
                ':status' => $status,
                ':gateway_tx_id' => $gatewayTxId
            ]);
        } catch (\PDOException $e) {
            error_log("Transaction Model Error (updateStatus): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates the gateway_transaction_id for a transaction using its local ID.
     */
    public function updateGatewayId(int $localTxId, string $gatewayTxId): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE transactions SET gateway_transaction_id = :gateway_tx_id WHERE id = :id"
            );
            return $stmt->execute([
                ':gateway_tx_id' => $gatewayTxId,
                ':id' => $localTxId
            ]);
        } catch (\PDOException $e) {
            error_log("Transaction Model Error (updateGatewayId): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates the status of a transaction using the gateway's ID.
     */
    public function updateStatusByGatewayId(string $gatewayTxId, string $status): bool
    {
        return $this->updateStatus($gatewayTxId, $status);
    }

    /**
     * Finds a transaction by its local primary key ID.
     */
    public function findById(int $id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM transactions WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("Transaction Model Error (findById): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Finds a transaction by its unique gateway transaction ID/reference.
     */
    public function findByGatewayTxId(string $gatewayTxId)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM transactions WHERE gateway_transaction_id = :gateway_tx_id LIMIT 1"
            );
            $stmt->bindParam(':gateway_tx_id', $gatewayTxId, \PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("Transaction Model Error (findByGatewayTxId): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculates the total revenue from all completed transactions.
     * Used for the admin dashboard.
     */
    public function getTotalRevenue(): float
    {
        try {
            $stmt = $this->db->query("SELECT SUM(amount) FROM transactions WHERE status = 'completed'");
            return (float) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Transaction Model Error (getTotalRevenue): " . $e->getMessage());
            return 0.00;
        }
    }
}