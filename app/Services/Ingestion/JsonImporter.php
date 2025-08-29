<?php

namespace App-Services-Ingestion;

use App-Models-Movie;

class JsonImporter implements ImporterInterface
{
    protected Movie $movieModel;
    protected string $webserverUser = 'villa3853'; // Change if your user is different

    public function __construct()
    {
        $this->movieModel = new Movie();
    }

    public function fetch(): array
    {
        echo "Fetching movie list from local JSON file...\n";
        $jsonPath = ROOT_PATH . '/data/public_domain_movies.json';
        if (!file_exists($jsonPath)) {
            echo "Error: Data file not found at {$jsonPath}.\n";
            return [];
        }
        $json = file_get_contents($jsonPath);
        return json_decode($json, true) ?? [];
    }

    public function process(array $item): void
    {
        $title = $item['title'];
        $identifier = $item['identifier'];
        $genre = $item['genre'] ?? 'Public Domain';

        if ($this->movieModel->adminMovieExistsBySource('public_domain', $identifier)) {
            echo "Skipping '{$title}' - already exists.\n";
            return;
        }

        echo "Processing new movie: '{$title}'...\n";
        
        $metaUrl = "https://archive.org/metadata/{$identifier}";
        
        // --- THIS IS THE FIX: Use cURL for ALL API calls ---
        $ch_meta = curl_init();
        curl_setopt($ch_meta, CURLOPT_URL, $metaUrl);
        curl_setopt($ch_meta, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_meta, CURLOPT_USERAGENT, 'VillaStudio Ingestion Engine/1.0');
        $metaResponse = curl_exec($ch_meta);
        curl_close($ch_meta);
        
        if (!$metaResponse) {
            echo " - Could not fetch metadata for {$identifier}. Skipping.\n";
            return;
        }
        $metaData = json_decode($metaResponse, true);
        // --- END OF FIX ---

        $description = $metaData['metadata']['description'][0] ?? $metaData['metadata']['notes'][0] ?? 'No description available.';
        $release_date_str = $metaData['metadata']['publicdate'][0] ?? $metaData['created'] ?? 'now';
        $release_date = date('Y-m-d', strtotime($release_date_str));

        $videoUrl = null;
        if (isset($metaData['files'])) {
            foreach ($metaData['files'] as $fileName => $fileInfo) {
                if (isset($fileInfo['format']) && $fileInfo['format'] === 'MPEG4') {
                    $videoUrl = "https://archive.org/download/{$identifier}/" . rawurlencode($fileName);
                    break;
                }
            }
        }
        
        if (!$videoUrl) {
            echo " - Could not find a suitable MP4 file in the metadata. Skipping.\n";
            return;
        }
        
        echo " - Found video URL: {$videoUrl}\n";
        echo " - Downloading video...\n";
        $uploadDir = PUBLIC_PATH . '/storage/uploads/ingested_videos/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
        
        $fileNameOnDisk = uniqid('movie_', true) . '.mp4';
        $localPath = $uploadDir . $fileNameOnDisk;
        
        $fp = fopen($localPath, 'w+');
        $ch_video = curl_init($videoUrl);
        curl_setopt($ch_video, CURLOPT_TIMEOUT, 300); 
        curl_setopt($ch_video, CURLOPT_FILE, $fp); 
        curl_setopt($ch_video, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch_video);
        curl_close($ch_video);
        fclose($fp);

        chmod($localPath, 0644); 
        chown($localPath, $this->webserverUser);
        
        $movieData = [
            'title' => $title, 'description' => $description, 'release_date' => $release_date,
            'poster_path' => "https://archive.org/download/{$identifier}/__ia_thumb.jpg",
            'video_path' => '/public/storage/uploads/ingested_videos/' . $fileNameOnDisk,
            'genre' => $genre, 'status' => 'published', 'is_premium' => 0,
            'source' => 'public_domain', 'source_id' => $identifier
        ];

        if ($this->movieModel->create($movieData)) {
            echo " - Successfully added to database!\n";
        } else {
            echo " - Failed to add to database.\n";
        }
    }
}