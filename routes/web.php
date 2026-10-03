<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    $latestNews = \App\Models\News::latest()->take(3)->get();
    $latestGalleries = \App\Models\Gallery::latest()->take(6)->get();
    return view('welcome', compact('latestNews', 'latestGalleries'));
});

Route::get('/tentang', function () {
    return view('tentang');
});

Route::get('/berita', [NewsController::class, 'index']);
Route::get('/berita/create', [NewsController::class, 'create']);
Route::post('/berita', [NewsController::class, 'store']);
Route::get('/berita/{news}/edit', [NewsController::class, 'edit']);
Route::put('/berita/{news}', [NewsController::class, 'update']);
Route::delete('/berita/{news}', [NewsController::class, 'destroy']);
Route::get('/berita/{news}', [NewsController::class, 'show']);
Route::get('/galeri', [GalleryController::class, 'index']);
Route::get('/galeri/create', [GalleryController::class, 'create']);
Route::post('/galeri', [GalleryController::class, 'store']);
Route::get('/galeri/{gallery}/edit', [GalleryController::class, 'edit']);
Route::put('/galeri/{gallery}', [GalleryController::class, 'update']);
Route::delete('/galeri/{gallery}', [GalleryController::class, 'destroy']);
Route::get('/galeri/{gallery}', [GalleryController::class, 'show']);
Route::get('/kontak', [ContactController::class, 'index']);
Route::get('/kontak/create', [ContactController::class, 'create']);
Route::post('/kontak', [ContactController::class, 'store']);
Route::get('/kontak/{contact}/edit', [ContactController::class, 'edit']);
Route::put('/kontak/{contact}', [ContactController::class, 'update']);
Route::delete('/kontak/{contact}', [ContactController::class, 'destroy']);
Route::get('/kontak/{contact}', [ContactController::class, 'show']);