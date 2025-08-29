<?php

namespace App\Models;

use App\Core\Database;

class Season
{
    protected \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Creates a new season for a given series.
     */
    public function create(int $seriesId, int $seasonNumber, ?string $title = null): bool
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO seasons (series_id, season_number, title) VALUES (:series_id, :season_number, :title)"
            );
            return $stmt->execute([
                ':series_id' => $seriesId,
                ':season_number' => $seasonNumber,
                ':title' => $title,
            ]);
        } catch (\PDOException $e) {
            error_log("Season Model Error (create): " . $e->getMessage());
            return false;
        }
    }
}