<?php
namespace App\Models;
use App\Core\Database;

class Series
{
    protected \PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }
    
    private function generateSlug(string $title): string
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }

    // --- Admin Methods ---
    public function adminGetAllPaginated(int $page = 1, int $perPage = 15): array
    {
        $countStmt = $this->db->query("SELECT COUNT(id) FROM series");
        $total = (int) $countStmt->fetchColumn();
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("SELECT * FROM series ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return ['series' => $stmt->fetchAll(), 'totalPages' => $totalPages, 'currentPage' => $page];
    }

    public function adminFindByIdWithDetails(int $id) { return $this->findWithDetails($id, true); }
    
    public function create(array $data): ?string
    {
        $slug = $this->generateSlug($data['title']);
        $sql = "INSERT INTO series (title, slug, description, release_date, poster_path, genre, status, is_premium, uploaded_by_user_id) VALUES (:title, :slug, :description, :release_date, :poster_path, :genre, :status, :is_premium, :uploaded_by_user_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':title' => $data['title'], ':slug' => $slug, ':description' => $data['description'], ':release_date' => $data['release_date'], ':poster_path' => $data['poster_path'], ':genre' => $data['genre'], ':status' => $data['status'], ':is_premium' => (int)($data['is_premium'] ?? 0), ':uploaded_by_user_id' => $data['uploaded_by_user_id'] ?? null]);
        return $this->db->lastInsertId();
    }
    
    // --- Public Methods ---
    public function getAllPaginated(int $page = 1, int $perPage = 12): array
    {
        $countStmt = $this->db->query("SELECT COUNT(id) FROM series WHERE status = 'published'");
        $total = (int) $countStmt->fetchColumn();
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("SELECT * FROM series WHERE status = 'published' ORDER BY release_date DESC LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return ['series' => $stmt->fetchAll(), 'totalPages' => $totalPages, 'currentPage' => $page];
    }
    
    public function findBySlugWithDetails(string $slug) { return $this->findWithDetails($slug, false); }

    private function findWithDetails($identifier, bool $isAdmin)
    {
        $sql = $isAdmin ? "SELECT * FROM series WHERE id = :identifier" : "SELECT * FROM series WHERE slug = :identifier AND status = 'published'";
        $seriesStmt = $this->db->prepare($sql . " LIMIT 1");
        $seriesStmt->execute([':identifier' => $identifier]);
        $series = $seriesStmt->fetch();
        if (!$series) return false;

        $seasonsStmt = $this->db->prepare("SELECT * FROM seasons WHERE series_id = :series_id ORDER BY season_number ASC");
        $seasonsStmt->execute([':series_id' => $series['id']]);
        $seasons = $seasonsStmt->fetchAll(\PDO::FETCH_ASSOC);

        $episodesStmt = $this->db->prepare("SELECT * FROM episodes WHERE season_id IN (SELECT id FROM seasons WHERE series_id = :series_id) ORDER BY season_id, episode_number ASC");
        $episodesStmt->execute([':series_id' => $series['id']]);
        $episodes = $episodesStmt->fetchAll(\PDO::FETCH_ASSOC);

        $series['seasons'] = [];
        foreach ($seasons as $season) {
            $season['episodes'] = array_values(array_filter($episodes, fn($ep) => $ep['season_id'] == $season['id']));
            $series['seasons'][] = $season;
        }
        return $series;
    }
}