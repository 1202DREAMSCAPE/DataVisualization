<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ChartCustomizer;
use App\Livewire\DataCleaning;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\ChartCustomizeController;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfilePageController;
use App\Http\Controllers\CsvCleaningController;

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

// CSV Data Cleaning Routes
Route::get('/clean-csv/upload', [CsvCleaningController::class, 'showUploadForm'])->name('clean-csv.upload.form');
Route::post('/clean-csv/upload', [CsvCleaningController::class, 'uploadCsv'])->name('clean-csv.upload');
Route::get('/clean-csv/preview', [CsvCleaningController::class, 'showPreview'])->name('clean-csv.preview');
Route::post('/clean-csv/clean', [CsvCleaningController::class, 'cleanCsv'])->name('clean-csv.clean');
Route::get('/clean-csv/cleaned', [CsvCleaningController::class, 'showCleaned'])->name('clean-csv.cleaned');
Route::get('/clean-csv/download', [CsvCleaningController::class, 'downloadCleanedCsv'])->name('clean-csv.download');


Route::post('/signup', [SignUpController::class, 'store'])->name('signup.store');

// Dashboard
Route::view('/login', 'login')
    ->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/'); // Redirect to the homepage after logout
})->name('logout');


// Route to display the profile page
Route::get('/profile', [ProfilePageController::class, 'show'])
    ->middleware('auth')
    ->name('profile');

// Route to update the profile (using PATCH for partial updates)
Route::patch('/profile', [ProfilePageController::class, 'update'])
    ->middleware('auth')
    ->name('profile.update');


// Data Cleaning Route
Route::get('/data-cleaning', DataCleaning::class)
    ->name('data-cleaning');

// Chart Customization
Route::get('/chart/customize/{type}', [ChartCustomizeController::class, 'customize'])
    ->name('chart.customize');

// Include Authentication Routes
require __DIR__.'/auth.php';
