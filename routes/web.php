<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;

// ---------- PUBLIC ----------
Route::get('/', function () {
    $latestNews = \App\Models\News::latest()->take(3)->get();
    $latestGalleries = \App\Models\Gallery::latest()->take(6)->get();
    return view('welcome', compact('latestNews', 'latestGalleries'));
})->name('home');

Route::get('/tentang', function () {
    return view('tentang');
})->name('tentang');

// News publik: read-only
Route::get('/berita', [NewsController::class, 'index'])->name('berita.index');
Route::get('/berita/{news}', [NewsController::class, 'show'])->name('berita.show');

// Gallery publik: read-only
Route::get('/galeri', [GalleryController::class, 'index'])->name('galeri.index');
Route::get('/galeri/{gallery}', [GalleryController::class, 'show'])->name('galeri.show');

// Contact publik: hanya lihat form + submit
Route::get('/kontak', [ContactController::class, 'create'])->name('kontak.create');
Route::post('/kontak', [ContactController::class, 'store'])->name('kontak.store');

// ---------- ADMIN AUTH ----------
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('admin.logout');

// ---------- ADMIN AREA ----------
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/berita', [AdminNewsController::class, 'index'])->name('berita.index');
    Route::get('/berita/create', [AdminNewsController::class, 'create'])->name('berita.create');
    Route::post('/berita', [AdminNewsController::class, 'store'])->name('berita.store');
    Route::get('/berita/{news}/edit', [AdminNewsController::class, 'edit'])->name('berita.edit');
    Route::put('/berita/{news}', [AdminNewsController::class, 'update'])->name('berita.update');
    Route::delete('/berita/{news}', [AdminNewsController::class, 'destroy'])->name('berita.destroy');

    Route::get('/galeri', [AdminGalleryController::class, 'index'])->name('galeri.index');
    Route::get('/galeri/create', [AdminGalleryController::class, 'create'])->name('galeri.create');
    Route::post('/galeri', [AdminGalleryController::class, 'store'])->name('galeri.store');
    Route::get('/galeri/{gallery}/edit', [AdminGalleryController::class, 'edit'])->name('galeri.edit');
    Route::put('/galeri/{gallery}', [AdminGalleryController::class, 'update'])->name('galeri.update');
    Route::delete('/galeri/{gallery}', [AdminGalleryController::class, 'destroy'])->name('galeri.destroy');

    Route::get('/kontak', [AdminContactController::class, 'index'])->name('kontak.index');
    Route::get('/kontak/{contact}', [AdminContactController::class, 'show'])->name('kontak.show');
    Route::get('/kontak/{contact}/edit', [AdminContactController::class, 'edit'])->name('kontak.edit');
    Route::put('/kontak/{contact}', [AdminContactController::class, 'update'])->name('kontak.update');
    Route::delete('/kontak/{contact}', [AdminContactController::class, 'destroy'])->name('kontak.destroy');
});
