<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('home');
})->name('home');

Route::get('/login', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('home');
});

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/preview', [DashboardController::class, 'previewRequest'])->name('previewRequest');
    Route::post('/delete-requests', [DashboardController::class, 'deleteRequests'])->name('deleteRequests');

    Route::post('/download/{fileUuid}', [DashboardController::class, 'downloadFile'])->name('downloadFile');
});

// Public webhook route
//Route::any('/{username}', [DashboardController::class, 'logRequest'])->name('logRequest');
Route::any('/{username}', [DashboardController::class, 'logRequest'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]) // Exempt CSRF
    ->name('logRequest');
