<?php

namespace App\Services\Ingestion;

use App\Models\Movie;

class PrelingerImporter implements ImporterInterface
{
    protected string $apiUrl = "https://archive.org/advancedsearch.php?q=collection%3A(prelinger)+AND+mediatype%3A(movies)&fl%5B%5D=description,identifier,title,publicdate&rows=50&page=1&output=json";
    protected Movie $movieModel;
    protected string $webserverUser = 'villa3853'; // IMPORTANT: Change this if your user is different

    public function __construct()
    {
        $this->movieModel = new Movie();
    }

    public function fetch(): array
    {
        echo "Fetching movie list from Internet Archive (Prelinger Archives)...\n";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            echo "Error: Failed to fetch data from API.\n";
            return [];
        }
        
        $data = json_decode($response, true);
        return $data['response']['docs'] ?? [];
    }
    
    public function process(array $apiMovie): void
    {
        $title = $apiMovie['title'] ?? 'Untitled';
        $identifier = $apiMovie['identifier'];

        if ($this->movieModel->adminMovieExistsBySource('public_domain', $identifier)) {
            echo "Skipping '{$title}' - already exists.\n";
            return;
        }

        echo "Processing new movie: '{$title}'...\n";
        
        $filesUrl = "https://archive.org/metadata/{$identifier}/files";
        $filesData = json_decode(@file_get_contents($filesUrl), true);
        $videoUrl = null;
        if (isset($filesData['result'])) {
            foreach ($filesData['result'] as $file) {
                if (isset($file['format']) && $file['format'] === 'MPEG4') {
                    $videoUrl = "https://archive.org/download/{$identifier}/{$file['name']}";
                    break;
                }
            }
        }
        
        if (!$videoUrl) {
            echo " - Could not find MP4. Skipping.\n";
            return;
        }
        
        echo " - Downloading video...\n";
        $uploadDir = PUBLIC_PATH . '/storage/uploads/ingested_videos/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
        
        $fileName = uniqid('movie_', true) . '.mp4';
        $localPath = $uploadDir . $fileName;
        
        $fp = fopen($localPath, 'w+');
        $ch = curl_init($videoUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        // --- THE PERMANENT PERMISSIONS FIX ---
        chmod($localPath, 0644);
        chown($localPath, $this->webserverUser);
        // --- END OF FIX ---
        
        echo "Download complete. Saved to: {$localPath}\n";
        
        $movieData = [
            'title' => $title,
            'description' => $apiMovie['description'][0] ?? 'No description available.',
            'release_date' => date('Y-m-d', strtotime($apiMovie['publicdate'])),
            'poster_path' => "https://archive.org/download/{$identifier}/__ia_thumb.jpg",
            'video_path' => '/public/storage/uploads/ingested_videos/' . $fileName,
            'genre' => 'Public Domain',
            'status' => 'published', 'is_premium' => 0,
            'source' => 'public_domain', 'source_id' => $identifier
        ];

        if ($this->movieModel->create($movieData)) {
            echo " - Successfully added to database!\n";
        } else {
            echo " - Failed to add to database.\n";
        }
    }
}