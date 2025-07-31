<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;


Route::post('/callback/{code}', [DocumentController::class, 'callback'])->name('document-callback');
Route::get('/get/{code}', [DocumentController::class, 'get'])->name('document-get');
Route::get('/get-pdf/{code}', [DocumentController::class, 'getPdf'])->name('get-pdf');
Route::get('/get-file/{code}', [DocumentController::class, 'getFile'])->name('get-file');
Route::get('/check-pdf/{code}/{i?}', [DocumentController::class, 'checkPdf'])->name('check-pdf');
