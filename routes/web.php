<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ChartCustomizer;
use App\Livewire\DataCleaning;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\ChartCustomizeController;

// Landing Page
Route::view('/', 'landing-page')->name('landing-page');

// Project Page
Route::view('/project', 'project')->name('project');

// Dashboard
Route::view('/dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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
