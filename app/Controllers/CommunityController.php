<?php
namespace App\Controllers;
use App\Models\Review;

class CommunityController
{
    public function index()
    {
        $reviewModel = new Review();
        $recentReviews = $reviewModel->getRecent(20);
        $data = [
            'title' => 'Community Hub - VillaStudio',
            'pageHeader' => 'Recent Community Activity',
            'reviews' => $recentReviews,
        ];
        return view('pages.community', $data);
    }
}