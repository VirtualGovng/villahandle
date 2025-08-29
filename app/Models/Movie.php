<?php

namespace App\Models;

use App\Core\Database;

class Movie
{
    protected \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function search(string $query, int $page = 1, int $perPage = 12): array
    {
        try {
            $searchTerm = '%' . $query . '%';
            $countSql = "SELECT COUNT(m.id) FROM movies m WHERE m.status = 'published' AND (m.title LIKE :queryTitle OR COALESCE(m.description, '') LIKE :queryDesc)";
            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute([':queryTitle' => $searchTerm, ':queryDesc' => $searchTerm]);
            $totalMovies = (int) $countStmt->fetchColumn();
            $totalPages = ceil($totalMovies / $perPage);
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM movies WHERE status = 'published' AND (title LIKE :queryTitle OR COALESCE(description, '') LIKE :queryDesc) ORDER BY release_date DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':queryTitle', $searchTerm, \PDO::PARAM_STR);
            $stmt->bindParam(':queryDesc', $searchTerm, \PDO::PARAM_STR);
            $stmt->bindParam(':limit', $perPage, \PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            return ['movies' => $stmt->fetchAll(), 'totalPages' => $totalPages, 'currentPage' => $page];
        } catch (\PDOException $e) {
            error_log("Movie Model Error (search): " . $e->getMessage());
            return ['movies' => [], 'totalPages' => 0, 'currentPage' => 1];
        }
    }
    
    // --- Admin-specific Methods ---

    public function adminGetAllPaginated(int $page = 1, int $perPage = 15): array
    {
        try {
            $countStmt = $this->db->query("SELECT COUNT(id) FROM movies");
            $totalMovies = (int) $countStmt->fetchColumn();
            $totalPages = ceil($totalMovies / $perPage);
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT m.*, u.username as creator_username 
                    FROM movies m
                    LEFT JOIN users u ON m.uploaded_by_user_id = u.id
                    ORDER BY m.created_at DESC 
                    LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':limit', $perPage, \PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            return ['movies' => $stmt->fetchAll(), 'totalPages' => $totalPages, 'currentPage' => $page];
        } catch (\PDOException $e) {
            error_log("Movie Model Error (adminGetAllPaginated): " . $e->getMessage());
            return ['movies' => [], 'totalPages' => 0, 'currentPage' => 1];
        }
    }

    public function adminFindById(int $id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM movies WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("Movie Model Error (adminFindById): " . $e->getMessage());
            return false;
        }
    }
    
    private function generateSlug(string $title): string
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }

    public function create(array $data): ?string
    {
        try {
            $slug = $this->generateSlug($data['title']);
            $sql = "INSERT INTO movies (title, slug, description, release_date, poster_path, video_path, genre, status, is_premium, uploaded_by_user_id, source, source_id) 
                    VALUES (:title, :slug, :description, :release_date, :poster_path, :video_path, :genre, :status, :is_premium, :uploaded_by_user_id, :source, :source_id)";
            $stmt = $this->db->prepare($sql);
            
            $stmt->execute([
                ':title' => $data['title'],
                ':slug' => $slug,
                ':description' => $data['description'],
                ':release_date' => $data['release_date'],
                ':poster_path' => $data['poster_path'],
                ':video_path' => $data['video_path'] ?? null,
                ':genre' => $data['genre'],
                ':status' => $data['status'],
                ':is_premium' => (int)($data['is_premium'] ?? 0),
                ':uploaded_by_user_id' => $data['uploaded_by_user_id'] ?? null,
                ':source' => $data['source'] ?? 'self-hosted',
                ':source_id' => $data['source_id'] ?? null,
            ]);
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            error_log("Movie Model Error (create): " . $e->getMessage());
            return null;
        }
    }

    public function update(int $movieId, array $data): bool
    {
        try {
            $slug = $this->generateSlug($data['title']);
            $sql = "UPDATE movies SET 
                        title = :title, slug = :slug, description = :description, 
                        release_date = :release_date, poster_path = :poster_path, video_path = :video_path, 
                        genre = :genre, status = :status, is_premium = :is_premium,
                        uploaded_by_user_id = :uploaded_by_user_id
                    WHERE id = :id";
            return $this->db->prepare($sql)->execute([
                ':title' => $data['title'],
                ':slug' => $slug,
                ':description' => $data['description'],
                ':release_date' => $data['release_date'],
                ':poster_path' => $data['poster_path'],
                ':video_path' => $data['video_path'],
                ':genre' => $data['genre'],
                ':status' => $data['status'],
                ':is_premium' => (int)($data['is_premium'] ?? 0),
                ':uploaded_by_user_id' => $data['uploaded_by_user_id'] ?? null,
                ':id' => $movieId,
            ]);
        } catch (\PDOException $e) {
            error_log("Movie Model Error (update): " . $e->getMessage());
            return false;
        }
    }
    
    public function delete(int $movieId): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM movies WHERE id = :id");
            return $stmt->execute([':id' => $movieId]);
        } catch (\PDOException $e) {
            error_log("Movie Model Error (delete): " . $e->getMessage());
            return false;
        }
    }

    // --- Public & Shared Methods ---

    public function getAllPaginated(int $page = 1, int $perPage = 12, ?string $genre = null): array
    {
        try {
            $countSql = "SELECT COUNT(id) FROM movies WHERE status = 'published'";
            $countParams = [];
            if ($genre) {
                $countSql .= " AND genre = :genre";
                $countParams[':genre'] = $genre;
            }
            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($countParams);
            $totalMovies = (int) $countStmt->fetchColumn();
            $totalPages = ceil($totalMovies / $perPage);
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM movies WHERE status = 'published'";
            if ($genre) {
                $sql .= " AND genre = :genre";
            }
            $sql .= " ORDER BY release_date DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            if ($genre) {
                $stmt->bindParam(':genre', $genre, \PDO::PARAM_STR);
            }
            $stmt->bindParam(':limit', $perPage, \PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            return ['movies' => $stmt->fetchAll(), 'totalPages' => $totalPages, 'currentPage' => $page];
        } catch (\PDOException $e) {
            error_log("Movie Model Error (getAllPaginated): " . $e->getMessage());
            return ['movies' => [], 'totalPages' => 0, 'currentPage' => 1];
        }
    }

    public function findBySlug(string $slug)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM movies WHERE status = 'published' AND slug = :slug LIMIT 1");
            $stmt->bindParam(':slug', $slug, \PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("Movie Model Error (findBySlug): " . $e->getMessage());
            return false;
        }
    }

    public function getByGenre($genres, int $limit = 6, string $orderBy = 'release_date'): array
    {
        try {
            if (is_array($genres)) {
                if (empty($genres)) return [];
                $inQuery = implode(',', array_fill(0, count($genres), '?'));
                $sql = "SELECT * FROM movies WHERE status = 'published' AND genre IN ({$inQuery}) ORDER BY {$orderBy} DESC LIMIT ?";
                $params = array_merge($genres, [$limit]);
            } else {
                $sql = "SELECT * FROM movies WHERE status = 'published' AND genre = ? ORDER BY {$orderBy} DESC LIMIT ?";
                $params = [$genres, $limit];
            }
            $stmt = $this->db->prepare($sql);
            $paramIndex = 1;
            foreach ($params as $value) {
                $stmt->bindValue($paramIndex, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
                $paramIndex++;
            }
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Movie Model Error (getByGenre): " . $e->getMessage());
            return [];
        }
    }

    public function getFeaturedMovie()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM movies WHERE status = 'published' ORDER BY is_premium DESC, release_date DESC LIMIT 1");
            $stmt->execute();
            return $stmt->fetch();
        } catch (\PDOException $e) {
            error_log("Movie Model Error (getFeaturedMovie): " . $e->getMessage());
            return false;
        }
    }

    public function countAll(): int
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(id) FROM movies WHERE status = 'published'");
            return (int) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Movie Model Error (countAll): " . $e->getMessage());
            return 0;
        }
    }
    
    public function getMoviesByCreatorId(int $creatorId): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM movies WHERE uploaded_by_user_id = :creator_id ORDER BY created_at DESC"
            );
            $stmt->execute([':creator_id' => $creatorId]);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Movie Model Error (getMoviesByCreatorId): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Checks if a movie from a specific source with a specific source ID already exists.
     * Used by the ingestion engine to prevent duplicates.
     */
    public function adminMovieExistsBySource(string $source, string $sourceId): bool
    {
        try {
            $stmt = $this->db->prepare("SELECT 1 FROM movies WHERE source = :source AND source_id = :source_id LIMIT 1");
            $stmt->execute([':source' => $source, ':source_id' => $sourceId]);
            return (bool) $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Movie Model Error (adminMovieExistsBySource): " . $e->getMessage());
            return false; // Fail safe
        }
    }
}