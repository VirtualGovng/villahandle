<?php

namespace App\Controllers;

use App\Models\Movie;

class SearchController
{
    /**
     * Display search results for a given query.
     */
    public function index()
    {
        // --- THIS IS THE FIX ---
        // Get the raw search query from the URL and trim any whitespace.
        // This is safer and more reliable than the old filter.
        $query = trim($_GET['q'] ?? '');
        // ---------------------

        if (empty($query)) {
            // If no query is provided, redirect to the movies page.
            redirect('/movies');
        }

        $movieModel = new Movie();
        $page = $_GET['page'] ?? 1;
        
        $paginationData = $movieModel->search($query, (int)$page);

        $data = [
            'title' => 'Search Results for "' . htmlspecialchars($query) . '"',
            'pageHeader' => 'Search Results for "' . htmlspecialchars($query) . '"',
            'query' => $query, // Pass the original query for display and pagination
            'movies' => $paginationData['movies'],
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $paginationData['currentPage'],
        ];
        
        return view('pages.search_results', $data);
    }
}