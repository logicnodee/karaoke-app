<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FoodBevController;
use App\Http\Controllers\RoomsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KasirController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/rooms', [RoomsController::class, 'index'])->name('rooms');
Route::get('/food-beverages', [FoodBevController::class, 'index'])->name('food-beverages');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Routes (General)
Route::middleware(['web'])->group(function () {

    Route::get('/dashboard/operator', [AuthController::class, 'operatorDashboard'])->name('dashboard.operator');

    // Kasir Dashboard Routes
    Route::name('kasir.')->prefix('kasir')->group(function () {
        Route::get('/', [KasirController::class, 'index'])->name('dashboard');
        Route::get('/ruangan', [KasirController::class, 'ruangan'])->name('ruangan');
        Route::get('/pemesanan', [KasirController::class, 'pemesanan'])->name('pemesanan');
        Route::view('/pesanan-aktif', 'dashboard.kasir.pesanan-aktif', ['active' => 'pesanan-aktif'])->name('pesanan-aktif');
        Route::view('/panggilan-room', 'dashboard.kasir.panggilan-room', ['active' => 'panggilan'])->name('panggilan');
        Route::get('/food-beverages', [KasirController::class, 'foodBeverages'])->name('food-beverages');
    });

    // Admin Dashboard Routes - Manually protected via session check in helper or controller for dummy auth
    Route::name('admin.')->prefix('admin')->group(function () {
        Route::get('/ringkasan', [AdminController::class, 'ringkasan'])->name('ringkasan');
        Route::get('/ruangan', [AdminController::class, 'ruangan'])->name('ruangan');
        Route::get('/lagu', [AdminController::class, 'lagu'])->name('lagu');
        Route::get('/keuangan', [AdminController::class, 'keuangan'])->name('keuangan');
        Route::get('/billing', [AdminController::class, 'billing'])->name('billing');
        Route::get('/akun', [AdminController::class, 'akun'])->name('akun');
        Route::get('/membership', [AdminController::class, 'membership'])->name('membership');
        Route::get('/pemesanan', [AdminController::class, 'pemesanan'])->name('pemesanan');
        Route::get('/reservasi-room', [AdminController::class, 'reservasiRoom'])->name('reservasi-room');
        Route::view('/pesanan-aktif', 'dashboard.admin.pesanan-aktif', ['active' => 'pesanan-aktif'])->name('pesanan-aktif');
        Route::view('/panggilan-room', 'dashboard.admin.panggilan-room', ['active' => 'panggilan'])->name('panggilan');
        Route::get('/food-beverages', [AdminController::class, 'foodBeverages'])->name('food-beverages');
        Route::get('/kategori', [AdminController::class, 'kategori'])->name('kategori');
        Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
        Route::get('/activity-log', [AdminController::class, 'activityLog'])->name('activity-log');
        Route::get('/manajemen-absensi', [AdminController::class, 'manajemenAbsensi'])->name('manajemen-absensi');
        Route::get('/operator', [AdminController::class, 'operator'])->name('operator');
        Route::get('/video/{filename}', [AdminController::class, 'streamVideo'])->name('stream.video');
    });

    // Legacy route redirect
    Route::get('/dashboard/admin', function() {
        return redirect()->route('admin.ringkasan');
    })->name('dashboard.admin');
});

// Public Video Streaming (with Range request support for seeking)
Route::get('/stream/video/{filename}', [AdminController::class, 'streamVideo'])->name('stream.video.public')->where('filename', '.*');

// Room Login Access (Customer Facing)
Route::name('room.')->prefix('room')->group(function () {
    Route::get('/login', [App\Http\Controllers\RoomAccessController::class, 'showLogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\RoomAccessController::class, 'login'])->name('login.post');
    Route::get('/dashboard', [App\Http\Controllers\RoomAccessController::class, 'dashboard'])->name('dashboard');
    Route::get('/logout', [App\Http\Controllers\RoomAccessController::class, 'logout'])->name('logout');
});
