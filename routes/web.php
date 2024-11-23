<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'landing-page');

Route::view('/project', 'project');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
