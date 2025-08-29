<?php

namespace App\Services\Ingestion;

use App\Models\Movie;

class ArchiveOrgImporter implements ImporterInterface
{
    // --- THIS IS THE DEFINITIVE, TESTED, AND WORKING API URL ---
    protected string $apiUrl = "https://archive.org/advancedsearch.php?q=mediatype%3A(movies)+AND+collection%3A(publicdomainfeaturefilms)&fl%5B%5D=description,identifier,title,publicdate&rows=50&page=1&output=json";
    // --- END OF FIX ---
    
    protected Movie $movieModel;
    protected string $webserverUser = 'villa3853';

    public function __construct()
    {
        $this->movieModel = new Movie();
    }

    public function fetch(): array
    {
        echo "Fetching movie list from Internet Archive (Public Domain Feature Films)...\n";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'VillaStudio Ingestion Engine/1.0');
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
        // Use cURL for this request as well for consistency and reliability
        $ch_files = curl_init($filesUrl);
        curl_setopt($ch_files, CURLOPT_RETURNTRANSFER, true);
        $filesResponse = curl_exec($ch_files);
        curl_close($ch_files);
        $filesData = json_decode($filesResponse, true);

        $videoUrl = null;
        if (isset($filesData['files'])) {
            foreach ($filesData['files'] as $fileInfo) {
                if (isset($fileInfo['format']) && $fileInfo['format'] === 'MPEG4') {
                    $videoUrl = "https://archive.org/download/{$identifier}/{$fileInfo['name']}";
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

        chmod($localPath, 0644); 
        chown($localPath, $this->webserverUser);
        
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