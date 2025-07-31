<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\pages\HomePage;
use App\Http\Controllers\pages\Page2;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\DocumentController;


Route::post('/callback/{id}', [DocumentController::class, 'callback'])->name('document-callback');
Route::get('/get/{id}', [DocumentController::class, 'get'])->name('document-get');
Route::get('/get-pdf/{code}', [DocumentController::class, 'getPdf'])->name('get-pdf');
Route::get('/get-file/{code}', [DocumentController::class, 'getFile'])->name('get-file');
