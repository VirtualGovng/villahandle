<?php

namespace App\Models;

use App\Core\Database;

class User
{
    protected \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // --- Admin-specific Methods ---

    /**
     * Fetches a paginated list of all users for the admin panel.
     */
    public function adminGetAllPaginated(int $page = 1, int $perPage = 15): array
    {
        try {
            $countStmt = $this->db->query("SELECT COUNT(id) FROM users");
            $totalUsers = (int) $countStmt->fetchColumn();
            $totalPages = ceil($totalUsers / $perPage);
            $offset = ($page - 1) * $perPage;

            $stmt = $this->db->prepare("SELECT id, username, email, first_name, last_name, role, status, created_at FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
            $stmt->bindParam(':limit', $perPage, \PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            $users = $stmt->fetchAll();

            return ['users' => $users, 'totalPages' => $totalPages, 'currentPage' => $page];
        } catch (\PDOException $e) {
            error_log("User Model Error (adminGetAllPaginated): " . $e->getMessage());
            return ['users' => [], 'totalPages' => 0, 'currentPage' => 1];
        }
    }

    /**
     * Updates a user's record from the admin panel.
     */
    public function update(int $userId, array $data): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE users SET username = :username, email = :email, first_name = :first_name, last_name = :last_name, role = :role, status = :status WHERE id = :id"
            );
            return $stmt->execute([
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':first_name' => $data['first_name'] ?? null,
                ':last_name' => $data['last_name'] ?? null,
                ':role' => $data['role'],
                ':status' => $data['status'],
                ':id' => $userId,
            ]);
        } catch (\PDOException $e) {
            error_log("User Model Error (update): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a user from the database.
     */
    public function delete(int $userId): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            return $stmt->execute([':id' => $userId]);
        } catch (\PDOException $e) {
            error_log("User Model Error (delete): " . $e->getMessage());
            return false;
        }
    }

    // --- Public & Shared Methods ---

    /**
     * Finds a user by their unique ID.
     */
    public function findById(int $id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("User Model Error (findById): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Finds a user by their email address.
     */
    public function findByEmail(string $email)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("User Model Error (findByEmail): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Creates a new user in the database.
     */
    public function create(array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_ARGON2ID);
        }

        try {
            $stmt = $this->db->prepare(
                "INSERT INTO users (username, email, password, first_name, last_name, role, status) 
                 VALUES (:username, :email, :password, :first_name, :last_name, :role, :status)"
            );
            
            $stmt->execute([
                ':username'   => $data['username'],
                ':email'      => $data['email'],
                ':password'   => $data['password'],
                ':first_name' => $data['first_name'] ?? null,
                ':last_name'  => $data['last_name'] ?? null,
                ':role'       => $data['role'] ?? 'user',
                ':status'     => $data['status'] ?? 'active'
            ]);

            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) {
                error_log("User Model Error (create): Duplicate email or username for {$data['email']}");
            } else {
                error_log("User Model Error (create): " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Verifies a user's password.
     */
    public function verifyPassword(string $plainPassword, string $hashedPassword): bool
    {
        return password_verify($plainPassword, $hashedPassword);
    }

    /**
     * Counts all users in the database.
     */
    public function countAll(): int
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(id) FROM users");
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("User Model Error (countAll): " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Gets a simple list of all users with the 'creator' role.
     * Used for dropdowns in the admin panel.
     */
    public function getAllCreators(): array
    {
        try {
            $stmt = $this->db->query("SELECT id, username FROM users WHERE role = 'creator'");
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("User Model Error (getAllCreators): " . $e->getMessage());
            return [];
        }
    }
}