<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\PaymentMethodController as AdminPaymentMethodController;

// ---------- PUBLIC ----------
Route::get('/', function () {
    $latestNews = \App\Models\News::latest()->take(3)->get();
    $latestGalleries = \App\Models\Gallery::latest()->take(6)->get();
    $featuredMenus = \App\Models\Menu::where('is_available', true)
        ->where('is_featured', true)
        ->orderBy('name')
        ->get();
    return view('welcome', compact('latestNews', 'latestGalleries', 'featuredMenus'));
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

// ---------- ORDER (CUSTOMER) ----------
Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');

Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/keranjang/{menu}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/keranjang/{menu}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/keranjang/{menu}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
Route::post('/checkout', [OrderController::class, 'store'])->name('orders.store');
Route::get('/pesanan/berhasil/{order_code}', [OrderController::class, 'success'])->name('orders.success');
Route::get('/lacak-pesanan', [OrderController::class, 'trackForm'])->name('orders.track');
Route::post('/lacak-pesanan', [OrderController::class, 'track'])->name('orders.track.post');
Route::get('/pesanan/{order_code}', [OrderController::class, 'show'])->name('orders.show');
Route::post('/pesanan/{order_code}/konfirmasi', [OrderController::class, 'confirm'])->name('orders.confirm');

// ---------- PAYMENT (CUSTOMER, MANUAL TRANSFER) ----------
// Diakses via order_code seperti tracking — tanpa login baru.
Route::get('/pembayaran/{order_code}', [PaymentController::class, 'show'])->name('payments.show');
Route::post('/pembayaran/{order_code}', [PaymentController::class, 'store'])->name('payments.store');

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

    Route::get('/menu', [AdminMenuController::class, 'index'])->name('menu.index');
    Route::get('/menu/create', [AdminMenuController::class, 'create'])->name('menu.create');
    Route::post('/menu', [AdminMenuController::class, 'store'])->name('menu.store');
    Route::get('/menu/{menu}/edit', [AdminMenuController::class, 'edit'])->name('menu.edit');
    Route::put('/menu/{menu}', [AdminMenuController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{menu}', [AdminMenuController::class, 'destroy'])->name('menu.destroy');
    Route::post('/menu/{menu}/featured', [AdminMenuController::class, 'toggleFeatured'])->name('menu.featured');

    Route::get('/kontak', [AdminContactController::class, 'index'])->name('kontak.index');
    Route::get('/kontak/{contact}', [AdminContactController::class, 'show'])->name('kontak.show');
    Route::post('/kontak/{contact}/reply', [AdminContactController::class, 'reply'])->name('kontak.reply');
    Route::get('/kontak/{contact}/edit', [AdminContactController::class, 'edit'])->name('kontak.edit');
    Route::put('/kontak/{contact}', [AdminContactController::class, 'update'])->name('kontak.update');
    Route::delete('/kontak/{contact}', [AdminContactController::class, 'destroy'])->name('kontak.destroy');

    Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/pesanan/{order}/approve', [AdminOrderController::class, 'approve'])->name('orders.approve');
    Route::post('/pesanan/{order}/reject', [AdminOrderController::class, 'reject'])->name('orders.reject');
    Route::post('/pesanan/{order}/advance', [AdminOrderController::class, 'advance'])->name('orders.advance');

    // Pembayaran: approve/reject bukti transfer (masih di detail order).
    Route::post('/pesanan/{order}/payment/approve', [AdminPaymentController::class, 'approve'])->name('payments.approve');
    Route::post('/pesanan/{order}/payment/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');

    // Kelola Metode Pembayaran (auth admin existing, tanpa login baru).
    Route::get('/metode-pembayaran', [AdminPaymentMethodController::class, 'index'])->name('payment-methods.index');
    Route::get('/metode-pembayaran/create', [AdminPaymentMethodController::class, 'create'])->name('payment-methods.create');
    Route::post('/metode-pembayaran', [AdminPaymentMethodController::class, 'store'])->name('payment-methods.store');
    Route::get('/metode-pembayaran/{payment_method}/edit', [AdminPaymentMethodController::class, 'edit'])->name('payment-methods.edit');
    Route::put('/metode-pembayaran/{payment_method}', [AdminPaymentMethodController::class, 'update'])->name('payment-methods.update');
    Route::delete('/metode-pembayaran/{payment_method}', [AdminPaymentMethodController::class, 'destroy'])->name('payment-methods.destroy');
});
