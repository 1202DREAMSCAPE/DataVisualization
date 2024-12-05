<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleSheetsController;
use App\Http\Controllers\HuggingFaceController;
use App\Http\Controllers\FileUploadController;



Route::view('/', 'landing-page');

Route::view('/project', 'project');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Landing page where users choose the functionality
Route::get('/chooseoption', function () {
    return view('choose-option');
})->name('choose-option');

// Route for AI-generated CSV page
Route::get('/generate-csv', function () {
    return view('generate-csv');
})->name('generate-csv');

// Route for File Upload page
Route::get('/upload-file', [FileUploadController::class, 'index'])->name('upload-file');

// Handle AI-generated CSV data submission
Route::post('/huggingface/generate', [HuggingFaceController::class, 'generateText'])->name('generate-text');

require __DIR__.'/auth.php';
