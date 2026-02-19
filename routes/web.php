<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\SummaryController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/upload', [UploadController::class, 'index'])->name('upload');
Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');
Route::post('/upload/check', [UploadController::class, 'check'])->name('upload.check');

Route::get('/summary', [SummaryController::class, 'index'])->name('summary');
Route::get('/summary', [SummaryController::class, 'index'])->name('summary');
Route::get('/summary/data', [SummaryController::class, 'getData'])->name('summary.data');
Route::post('/summary/export', [SummaryController::class, 'exportExcel'])->name('summary.export');
Route::post('/summary/export-pdf', [App\Http\Controllers\SummaryController::class, 'exportPDF'])->name('summary.export_pdf');