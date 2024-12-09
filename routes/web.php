<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ChartCustomizer;
use App\Livewire\DataCleaning;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\ChartCustomizeController;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Auth;

// Landing Page
Route::view('/', 'landing-page')->name('landing-page');

// Project Page
Route::view('/project', 'project')->name('project');

// Dashboard
Route::view('/dashboard', 'dashboard')
    ->name('dashboard');

// Dashboard
Route::view('/signup', 'signup')
    ->name('signup');

Route::post('/signup', [SignUpController::class, 'store'])->name('signup.store');

// Dashboard
Route::view('/login', 'login')
    ->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/'); // Redirect to the homepage after logout
})->name('logout');


// Profile Page
Route::view('/profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Data Cleaning Route
Route::get('/data-cleaning', DataCleaning::class)
    ->name('data-cleaning');

// Chart Customization
Route::get('/chart/customize/{type}', [ChartCustomizeController::class, 'customize'])
    ->name('chart.customize');

// Include Authentication Routes
require __DIR__.'/auth.php';
