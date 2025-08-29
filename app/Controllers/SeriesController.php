<?php
namespace App\Controllers;
use App\Models\Series;
use App\Core\Router;

class SeriesController
{
    public function index()
    {
        $seriesModel = new Series();
        $page = $_GET['page'] ?? 1;
        $paginationData = $seriesModel->getAllPaginated((int)$page);
        $data = [
            'title' => 'Browse TV Series - VillaStudio',
            'pageHeader' => 'All Series',
            'series' => $paginationData['series'],
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $paginationData['currentPage'],
        ];
        return view('pages.series_list', $data);
    }

    public function show(string $slug)
    {
        $seriesModel = new Series();
        $series = $seriesModel->findBySlugWithDetails($slug);
        if (!$series) return Router::handleNotFound();
        $data = ['title' => $series['title'] . ' - VillaStudio', 'series' => $series];
        return view('pages.series_detail', $data);
    }
}