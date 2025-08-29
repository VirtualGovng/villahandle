<?php

namespace App\Services\Ingestion;

use App\Models\Movie;

class ArchiveOrgImporter implements ImporterInterface
{
    protected string $apiUrl = "https://archive.org/advancedsearch.php?q=collection%3A(publicdomainfeaturefilms)+AND+mediatype%3A(movies)&fl%5B%5D=description,identifier,title,publicdate&rows=50&page=1&output=json";
    protected Movie $movieModel;
    protected string $webserverUser = 'villa3 ઉ853';

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
        
        $metaUrl = "https://archive.org/metadata/{$identifier}";
        
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

        $description = $metaData['metadata']['description'][0] ?? $metaData['metadata']['notes'][0] ?? 'No description available.';
        $release_date_str = $metaData['metadata']['publicdate'][0] ?? $metaData['created'] ?? 'now';
        $release_date = date('Y-m-d', strtotime($release_date_str));

        $videoUrl = null;
        if (isset($metaData['files'])) {
            // Loop through the indexed array and check the 'name' property of each object.
            foreach ($metaData['files'] as $fileInfo) {
                if (isset($fileInfo['name']) && str_ends_with(strtolower($fileInfo['name']), '.mp4')) {
                    $fileName = ltrim($fileInfo['name'], './');
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
        curl_setopt($ch_video, CURLOPT_TIMEOUT, 600); 
        curl_setopt($ch_video, CURLOPT_FILE, $fp); 
        curl_setopt($ch_video, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch_video);
        $http_code = curl_getinfo($ch_video, CURLINFO_HTTP_CODE);
        curl_close($ch_video);
        fclose($fp);

        if ($http_code !== 200) {
            echo " - Download failed with HTTP status code: {$http_code}. Skipping.\n";
            unlink($localPath);
            return;
        }

        chmod($localPath, 0644); 
        chown($localPath, $this->webserverUser);
        
        $movieData = [
            'title' => $title, 'description' => $description, 'release_date' => $release_date,
            'poster_path' => "https://archive.org/download/{$identifier}/__ia_thumb.jpg",
            'video_path' => '/public/storage/uploads/ingested_videos/' . $fileNameOnDisk,
            'genre' => 'Public Domain', 'status' => 'published', 'is_premium' => 0,
            'source' => 'public_domain', 'source_id' => $identifier
        ];

        if ($this->movieModel->create($movieData)) {
            echo " - Successfully added to database!\n";
        } else {
            echo " - Failed to add to database.\n";
        }
    }
}