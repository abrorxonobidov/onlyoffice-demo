<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\pages\HomePage;
use App\Http\Controllers\pages\Page2;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\DocumentController;

// Main Page Route
Route::get('/', [HomePage::class, 'index'])->name('pages-home');
Route::get('/page-2', [Page2::class, 'index'])->name('pages-page-2');

// locale
Route::get('/lang/{locale}', [LanguageController::class, 'swap']);
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');

// authentication
Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');


Route::get('/document/index', [DocumentController::class, 'index'])->name('document-index');
Route::post('/upload', [DocumentController::class, 'upload'])->name('document-upload');
Route::get('/edit/{id}', [DocumentController::class, 'edit'])->name('document-edit');
Route::get('/get/{id}', [DocumentController::class, 'get'])->name('document-get');
Route::post('/delete', [DocumentController::class, 'delete'])->name('document-delete');
