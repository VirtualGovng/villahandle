<?php
namespace App\Controllers\Admin;

use App\Core\Registry;
use App\Models\Series;
use App\Models\Season;
use App\Models\Episode;
use App\Services\UploadService;

class SeriesController
{
    public function index()
    {
        $seriesModel = new Series();
        $page = $_GET['page'] ?? 1;
        $paginationData = $seriesModel->adminGetAllPaginated((int)$page);

        $data = [
            'title' => 'Manage Series',
            'user' => Registry::get('user'),
            'series' => $paginationData['series'],
            'totalPages' => $paginationData['totalPages'],
            'currentPage' => $paginationData['currentPage'],
        ];
        return view('admin.pages.series.index', $data, 'admin.layouts.admin_layout');
    }

    public function create()
    {
        $data = ['title' => 'Add New Series', 'user' => Registry::get('user')];
        return view('admin.pages.series.create', $data, 'admin.layouts.admin_layout');
    }
    
    public function store()
    {
        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'release_date' => $_POST['release_date'],
            'poster_path' => $_POST['poster_path'],
            'genre' => $_POST['genre'],
            'status' => $_POST['status'],
            'is_premium' => $_POST['is_premium'] ?? 0,
        ];
        
        $seriesId = (new Series())->create($data);
        if ($seriesId) {
            $_SESSION['success_message'] = 'Series created successfully. Now you can add seasons and episodes.';
            redirect('/admin/series/' . $seriesId . '/manage');
        } else {
            $_SESSION['error_message'] = 'Failed to create series.';
            redirect('/admin/series/create');
        }
    }

    public function manage(string $id)
    {
        $seriesModel = new Series();
        $series = $seriesModel->adminFindByIdWithDetails((int)$id);
        if (!$series) redirect('/admin/series');
        
        $data = [
            'title' => 'Manage: ' . $series['title'],
            'user' => Registry::get('user'),
            'series' => $series,
        ];
        return view('admin.pages.series.manage', $data, 'admin.layouts.admin_layout');
    }
    
    public function storeSeason(string $seriesId)
    {
        $seasonNumber = filter_input(INPUT_POST, 'season_number', FILTER_VALIDATE_INT);
        $title = trim($_POST['title']);
        (new Season())->create((int)$seriesId, $seasonNumber, $title);
        $_SESSION['success_message'] = 'Season added successfully.';
        redirect('/admin/series/' . $seriesId . '/manage');
    }

    public function storeEpisode(string $seasonId)
    {
        $uploadService = new UploadService();
        $videoPath = $uploadService->handleVideoUpload($_FILES['video_file'] ?? []);
        if (!$videoPath) {
            $_SESSION['error_message'] = 'Video file upload failed.';
            redirect($_SERVER['HTTP_REFERER']); // Go back to the manage page
        }

        $data = [
            'season_id' => (int)$seasonId,
            'episode_number' => $_POST['episode_number'],
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'video_path' => $videoPath,
        ];
        (new Episode())->create($data);
        $_SESSION['success_message'] = 'Episode added successfully.';
        redirect($_SERVER['HTTP_REFERER']);
    }
}