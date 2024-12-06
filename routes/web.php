<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ChartCustomizer;
use App\Livewire\DataCleaning;



Route::view('/', 'landing-page');

Route::view('/project', 'project');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


 Route::get('/data-cleaning', DataCleaning::class)->name('data-cleaning');
    

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Route for chart customization
use App\Http\Controllers\ChartController;

Route::get('/chart/customize/{type}', [ChartController::class, 'customizeChart'])->name('chart.customize');

require __DIR__.'/auth.php';
