<?php

use App\Core\Router;
use App\Controllers;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| This file defines all application routes. Middleware is passed as the third argument.
*/

// --- Public & Informational Routes ---
Router::get('/', [Controllers\HomeController::class, 'index']);
Router::get('/movies', [Controllers\MovieController::class, 'index']);
Router::get('/movies/{slug}', [Controllers\MovieController::class, 'show']);
Router::get('/search', [Controllers\SearchController::class, 'index']);
Router::get('/series', [Controllers\SeriesController::class, 'index']);
Router::get('/series/{slug}', [Controllers\SeriesController::class, 'show']);
Router::get('/community', [Controllers\CommunityController::class, 'index']);
Router::get('/about', [Controllers\PageController::class, 'about']);
Router::get('/contact', [Controllers\PageController::class, 'contact']);
Router::get('/faq', [Controllers\PageController::class, 'faq']);
Router::get('/terms', [Controllers\PageController::class, 'terms']);
Router::get('/privacy', [Controllers\PageController::class, 'privacy']);
Router::get('/copyright', [Controllers\PageController::class, 'copyright']);

// --- Authentication Routes ---
Router::get('/login', [Controllers\AuthController::class, 'showLoginForm'], 'guest');
Router::post('/login', [Controllers\AuthController::class, 'login'], 'guest');
Router::get('/register', [Controllers\AuthController::class, 'showRegistrationForm'], 'guest');
Router::post('/register', [Controllers\AuthController::class, 'register'], 'guest');
Router::get('/logout', [Controllers\AuthController::class, 'logout'], 'auth');

// --- Authenticated User Routes ---
Router::get('/profile', [Controllers\UserController::class, 'profile'], 'auth');
Router::get('/profile/subscription', [Controllers\UserController::class, 'subscription'], 'auth');
Router::get('/watch/{slug}', [Controllers\MovieController::class, 'watch'], 'auth');
Router::post('/reviews/store', [Controllers\ReviewController::class, 'store'], 'auth');

// --- Subscription and Payment Routes ---
Router::get('/subscribe', [Controllers\PaymentController::class, 'plans'], 'auth');
Router::post('/payment/initiate', [Controllers\PaymentController::class, 'initiate'], 'auth');
Router::get('/payment/callback', [Controllers\PaymentController::class, 'callback']);
Router::get('/payment/success', [Controllers\PaymentController::class, 'success']);
Router::get('/payment/cancel', [Controllers\PaymentController::class, 'cancel']);

// --- Creator Portal Routes ---
Router::get('/creator/dashboard', [Controllers\CreatorController::class, 'dashboard'], 'creator');
Router::get('/creator/movies/upload', [Controllers\CreatorController::class, 'showUploadForm'], 'creator');
Router::post('/creator/movies/store', [Controllers\CreatorController::class, 'store'], 'creator');
Router::get('/creator/movies/{id}/edit', [Controllers\CreatorController::class, 'editMovie'], 'creator');
Router::post('/creator/movies/{id}/update', [Controllers\CreatorController::class, 'updateMovie'], 'creator');

// --- Admin Panel Routes ---
Router::get('/admin', [Controllers\Admin\DashboardController::class, 'index'], 'admin');
Router::get('/admin/dashboard', [Controllers\Admin\DashboardController::class, 'index'], 'admin');

// User Management
Router::get('/admin/users', [Controllers\Admin\UserController::class, 'index'], 'admin');
Router::get('/admin/users/{id}/edit', [Controllers\Admin\UserController::class, 'edit'], 'admin');
Router::post('/admin/users/{id}/update', [Controllers\Admin\UserController::class, 'update'], 'admin');
Router::post('/admin/users/{id}/delete', [Controllers\Admin\UserController::class, 'destroy'], 'admin');

// Movie Management
Router::get('/admin/movies', [Controllers\Admin\MovieController::class, 'index'], 'admin');
Router::get('/admin/movies/create', [Controllers\Admin\MovieController::class, 'create'], 'admin');
Router::post('/admin/movies/store', [Controllers\Admin\MovieController::class, 'store'], 'admin');
Router::get('/admin/movies/{id}/edit', [Controllers\Admin\MovieController::class, 'edit'], 'admin');
Router::post('/admin/movies/{id}/update', [Controllers\Admin\MovieController::class, 'update'], 'admin');
Router::post('/admin/movies/{id}/delete', [Controllers\Admin\MovieController::class, 'destroy'], 'admin');

// Series Management
Router::get('/admin/series', [Controllers\Admin\SeriesController::class, 'index'], 'admin');
Router::get('/admin/series/create', [Controllers\Admin\SeriesController::class, 'create'], 'admin');
Router::post('/admin/series/store', [Controllers\Admin\SeriesController::class, 'store'], 'admin');
Router::get('/admin/series/{id}/manage', [Controllers\Admin\SeriesController::class, 'manage'], 'admin');
Router::post('/admin/series/{id}/seasons/store', [Controllers\Admin\SeriesController::class, 'storeSeason'], 'admin');
Router::post('/admin/seasons/{id}/episodes/store', [Controllers\Admin\SeriesController::class, 'storeEpisode'], 'admin');

// Other Admin Routes
Router::get('/admin/subscriptions', [Controllers\Admin\SubscriptionController::class, 'index'], 'admin');
Router::get('/admin/settings', [Controllers\Admin\SettingsController::class, 'index'], 'admin');
