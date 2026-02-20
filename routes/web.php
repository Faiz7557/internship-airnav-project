<?php

use Illuminate\Support\Facades\Route;

if (file_exists(__DIR__ . '/debug.php')) {
    require __DIR__ . '/debug.php';
}
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\DashboardController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/upload', [UploadController::class, 'index'])->name('upload');
Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');
Route::post('/upload/check', [UploadController::class, 'check'])->name('upload.check');

Route::get('/summary', [SummaryController::class, 'index'])->name('summary');

Route::get('/summary/data', [SummaryController::class, 'getData'])->name('summary.data');
Route::post('/summary/export', [SummaryController::class, 'exportExcel'])->name('summary.export');

Route::post('/dashboard/events', [EventController::class, 'store'])->name('dashboard.events.store');
Route::delete('/dashboard/events/{event}', [EventController::class, 'destroy'])->name('dashboard.events.destroy');
Route::post('/summary/export-pdf', [App\Http\Controllers\SummaryController::class, 'exportPDF'])->name('summary.export_pdf');
