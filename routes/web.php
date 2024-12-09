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


Route::get('/saved-charts', function () {
        return view('savedchartsmain');
    })->name('saved-charts');

    Route::get('/debug-charts', function() {
        return [
            'charts' => App\Models\SavedChart::all(),
            'user' => auth()->user()
        ];
    });


    Route::delete('/chart/{id}', [ChartCustomizeController::class, 'destroyChart'])->name('chart.delete');
    
Route::delete('/delete-chart/{id}', [ChartController::class, 'destroy']);


    Route::post('/chart/save', [ChartCustomizeController::class, 'saveChart'])
    ->name('chart.save')
    ->middleware('auth');


// Include Authentication Routes
require __DIR__.'/auth.php';
