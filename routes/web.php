<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ChartCustomizer;
use App\Livewire\ChartSelector;
use App\Livewire\DataCleaning;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\ChartCustomizeController;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfilePageController;
use App\Http\Controllers\CsvCleaningController;
use App\Http\Controllers\DeleteController;

Route::get('/build-charts', [ChartController::class, 'index'])->name('build-charts');

Route::group(['middleware' => 'guest'], function () {
    Route::view('/signup', 'signup')->name('signup');
    // Dashboard
    Route::view('/login', 'login')->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::view('/', 'landing-page')->name('landing-page');
    Route::post('/signup', [SignUpController::class, 'store'])->name('signup.store');

});
// Landing Page

// Project Page
Route::view('/project', 'project')->name('project');

// Dashboard
Route::view('/dashboard', 'dashboard')
    ->name('dashboard');

// CSV Data Cleaning Routes
Route::get('/clean-csv/upload', [CsvCleaningController::class, 'showUploadForm'])->name('clean-csv.upload.form');
Route::post('/clean-csv/upload', [CsvCleaningController::class, 'uploadCsv'])->name('clean-csv.upload');
Route::get('/clean-csv/preview', [CsvCleaningController::class, 'showPreview'])->name('clean-csv.preview');
Route::post('/clean-csv/clean', [CsvCleaningController::class, 'cleanCsv'])->name('clean-csv.clean');
Route::get('/clean-csv/cleaned', [CsvCleaningController::class, 'showCleaned'])->name('clean-csv.cleaned');
Route::get('/clean-csv/download', [CsvCleaningController::class, 'downloadCleanedCsv'])->name('clean-csv.download');

use App\Livewire\CsvUpload;
use App\Livewire\CsvPreview;
use App\Livewire\CsvCleaned;

Route::get('/csv-upload', CsvUpload::class)->name('csv-upload');
Route::get('/csv-preview', CsvPreview::class)->name('csv-preview');
Route::get('/csv-cleaned', CsvCleaned::class)->name('csv-cleaned');




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


    Route::middleware(['auth'])->group(function () {
        Route::get('/chart-selector', ChartSelector::class)->name('chart.selector');
    });
    
    
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

    use App\Http\Controllers\AiInsightsController;

    Route::post('/generate-insights', [AiInsightsController::class, 'generateInsights']);
    

    Route::delete('/charts/{id}', [DeleteController::class, 'deleteChart'])->name('charts.delete');


    Route::delete('/chart/{id}', [ChartCustomizeController::class, 'destroyChart'])->name('chart.delete');
    
Route::delete('/delete-chart/{id}', [ChartController::class, 'destroy']);


    Route::post('/chart/save', [ChartCustomizeController::class, 'saveChart'])
    ->name('chart.save')
    ->middleware('auth');


// Include Authentication Routes
require __DIR__.'/auth.php';
