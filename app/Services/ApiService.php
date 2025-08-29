<?php

namespace App\Services;

class ApiService
{
    protected string $tmdbApiKey;
    protected string $tmdbApiBaseUrl = 'https://api.themoviedb.org/3';

    public function __construct()
    {
        $this->tmdbApiKey = env('TMDB_API_KEY');
        if (empty($this->tmdbApiKey)) {
            // In a real application, this should throw an exception or be handled gracefully.
            error_log('TMDB API Key is not configured in the .env file.');
        }
    }

    /**
     * Fetches movie details from TMDB using a movie's title.
     * This is useful for enriching data we have in our local database.
     *
     * @param string $title The title of the movie to search for.
     * @return array|null The first movie result from the API, or null on failure.
     */
    public function getTmdbDetailsByTitle(string $title): ?array
    {
        if (empty($this->tmdbApiKey)) {
            return null;
        }

        $endpoint = "{$this->tmdbApiBaseUrl}/search/movie";
        $queryParams = http_build_query([
            'api_key' => $this->tmdbApiKey,
            'query' => $title,
            'page' => 1
        ]);
        
        $url = "{$endpoint}?{$queryParams}";

        // Use cURL for robust API requests
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            error_log("TMDB API Error: Failed to fetch data for title '{$title}'. HTTP Code: {$httpCode}");
            return null;
        }

        $data = json_decode($response, true);
        
        // Return the first and most relevant search result
        return $data['results'][0] ?? null;
    }

    /**
     * Constructs the full URL for a TMDB image.
     *
     * @param string|null $path The path from the API response (e.g., /qA5k3t_nI62uGEv4j1i32p1wgh.jpg).
     * @param string $size The desired image size (e.g., 'w500', 'original').
     * @return string The full image URL or a placeholder if path is null.
     */
    public function getImageUrl(?string $path, string $size = 'w500'): string
    {
        if (empty($path)) {
            if ($size === 'original') {
                return 'https://placehold.co/1920x1080/0f1014/1a1d23?text=No+Backdrop';
            }
            return 'https://placehold.co/500x750/1a1d23/ffffff?text=No+Poster';
        }
        return "https://image.tmdb.org/t/p/{$size}" . $path;
    }
}