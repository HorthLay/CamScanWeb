<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// tab guest

Route::middleware(['guest'])->group(function () {
    Route::get('/',      [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// tab auth + active
Route::middleware(['auth','active'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

     Route::middleware('tab.access:detection')->group(function () {
        Route::get('/detection', fn() => view('coming-soon', [
            'page' => 'Detection',
        ]))->name('detection');
    });

    Route::middleware('tab.access:reports')->group(function () {
        Route::get('/reports', fn() => view('coming-soon', [
            'page' => 'Reports',
        ]))->name('reports');
    });

    Route::middleware('tab.access:users')->group(function () {
        Route::get('/users',                        [UserController::class, 'index'])->name('users');
        Route::post('/users',                       [UserController::class, 'store'])->name('users.store');
        Route::post('/users/capture-register',      [UserController::class, 'captureRegister'])->name('users.capture-register');
        Route::put('/users/{user}',                  [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}',               [UserController::class, 'destroy'])->name('users.destroy');

        // Face verification — two steps
        Route::post('/users/{user}/verify-face',     [UserController::class, 'verifyFace'])->name('user.verify-face');
        Route::post('/users/{user}/register-face',   [UserController::class, 'registerFace'])->name('user.register-face');
    });

    Route::middleware('tab.access:settings')->group(function () {
        Route::get('/settings', fn() => view('coming-soon', [
            'page' => 'Settings',
        ]))->name('settings');
    });

    Route::middleware('tab.access:role-management')->group(function () {
        Route::get('/roles', fn() => view('coming-soon', [
            'page' => 'Role management',
        ]))->name('roles.index');
    });

    Route::middleware('tab.access:video-management')->group(function () {
        Route::get('/videos', fn() => view('coming-soon', [
            'page' => 'Video management',
        ]))->name('videos.index');
    });
   
});



   
