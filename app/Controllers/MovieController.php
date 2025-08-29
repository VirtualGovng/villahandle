<?php

namespace App\Controllers;

use App\Models\Movie;
use App\Core\Router;
use App\Services\ApiService;
use App\Services\AuthService;

class MovieController
{
    /**
     * Display a paginated listing of all movies.
     */
    public function index()
    {
        $movieModel = new Movie();
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $genre = isset($_GET['genre']) ? filter_var($_GET['genre'], FILTER_SANITIZE_STRING) : null;
        
        $paginationData = $movieModel->getAllPaginated($currentPage, 12, $genre);
        $pageTitle = $genre ? ucwords(str_replace('-', ' ', $genre)) . ' Movies' : 'All Movies';

        $data = [
            'title' => $pageTitle . ' - VillaStudio',
            'pageHeader' => $pageTitle,
            'movies' => $paginationData['movies'],
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $paginationData['currentPage'],
            'genre' => $genre
        ];
        
        return view('pages.movies', $data);
    }
    
    /**
     * Display a specific movie's detail page, enriched with API data.
     */
    public function show(string $slug)
    {
        $movieModel = new Movie();
        $apiService = new ApiService();
        
        $movie = $movieModel->findBySlug($slug);

        if (!$movie) {
            return Router::handleNotFound();
        }
        
        $tmdbDetails = $apiService->getTmdbDetailsByTitle($movie['title']);
        $relatedMovies = $movieModel->getByGenre($movie['genre'], 4);

        $data = [
            'title' => $movie['title'] . ' - VillaStudio',
            'movie' => $movie,
            'tmdb' => $tmdbDetails,
            'apiService' => $apiService,
            'relatedMovies' => $relatedMovies
        ];
        
        return view('pages.movie_detail', $data);
    }
    
    /**
     * Display the video player page for a specific movie.
     * Includes premium content and ad-display checks.
     */
    public function watch(string $slug)
    {
        $movieModel = new Movie();
        $movie = $movieModel->findBySlug($slug);

        // Security Check 1: If movie doesn't exist or has no video file, show 404.
        if (!$movie || empty($movie['video_path'])) {
            return Router::handleNotFound();
        }
        
        // Security Check 2: If the movie is marked as premium...
        if ($movie['is_premium'] == 1) {
            // ...then we MUST check if the currently logged-in user has an active subscription.
            if (!AuthService::userHasActiveSubscription()) {
                // If they don't, set an error message and redirect them to the subscription page.
                $_SESSION['error_message'] = 'You must have an active subscription to watch this premium movie.';
                redirect('/subscribe');
            }
        }

        // --- NEW LOGIC FOR ADS ---
        // Determine if ads should be shown for this movie.
        // Ads are shown only if the movie is NOT premium.
        $showAds = ($movie['is_premium'] == 0);
        // --- END OF NEW LOGIC ---

        // If all checks pass, the user is authorized to watch.
        $data = [
            'title' => 'Watching: ' . $movie['title'],
            'movie' => $movie,
            'showAds' => $showAds // Pass the decision to the view
        ];
        
        return view('pages.watch', $data, 'layouts.watch_layout');
    }
}