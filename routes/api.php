<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Middleware\JwtMiddleware;


Route::middleware(JwtMiddleware::class)->group(function () {
  Route::post('/callback/{code}', [DocumentController::class, 'callback'])->name('document-callback');
  Route::get('/get-file/{code}', [DocumentController::class, 'getFile'])->name('get-file');
});
Route::get('/get-pdf/{code}', [DocumentController::class, 'getPdf'])->name('get-pdf');
Route::get('/check-pdf/{code}/{i?}', [DocumentController::class, 'checkPdf'])->name('check-pdf');
