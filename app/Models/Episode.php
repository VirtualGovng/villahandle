<?php

namespace App\Models;

use App\Core\Database;

class Episode
{
    protected \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Creates a new episode for a given season.
     */
    public function create(array $data): bool
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO episodes (season_id, episode_number, title, description, video_path, duration_minutes) 
                 VALUES (:season_id, :episode_number, :title, :description, :video_path, :duration_minutes)"
            );
            return $stmt->execute([
                ':season_id' => $data['season_id'],
                ':episode_number' => $data['episode_number'],
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':video_path' => $data['video_path'],
                ':duration_minutes' => $data['duration_minutes'] ?? null,
            ]);
        } catch (\PDOException $e) {
            error_log("Episode Model Error (create): " . $e->getMessage());
            return false;
        }
    }
}