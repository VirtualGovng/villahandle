<?php

namespace App\Controllers;

use App\Models\Movie;

class HomeController
{
    /**
     * Show the application's home page.
     * This method fetches data for different sections of the homepage
     * to showcase a balanced mix of global, Nigerian, and African content.
     */
    public function index()
    {
        $movieModel = new Movie();

        // Fetch different categories of movies for the homepage
        $featuredMovie = $movieModel->getFeaturedMovie();
        $globalHits = $movieModel->getByGenre('General', 6);
        $nollywoodMovies = $movieModel->getByGenre('Nollywood', 6);
        $africanOriginals = $movieModel->getByGenre(['African Original', 'Ghallywood'], 6);

        // Prepare data to be passed to the view
        $data = [
            'title' => 'VillaStudio - Global Movies, African Stories',
            'featuredMovie' => $featuredMovie,
            'globalHits' => $globalHits,
            'nollywoodMovies' => $nollywoodMovies,
            'africanOriginals' => $africanOriginals,
        ];
        
        // Call the global view() helper function.
        // It will load the main layout, which in turn will load the 'pages.home' content.
        return view('pages.home', $data);
    }
}