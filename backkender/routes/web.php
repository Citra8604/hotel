<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Halaman Utama
Route::get('/', function () {
    return view('welcome');
});

// Autentikasi
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Registrasi Tamu (Poin 1 Tamu)
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Group Route yang butuh Login
Route::middleware(['auth'])->group(function () {
    
    // Redirect Dashboard Utama berdasarkan Role
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Group khusus Admin (Poin 2, 3, 4 Admin)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/rooms', [AdminController::class, 'rooms'])->name('rooms');
        
        // Route ini akan kita isi nanti untuk CRUD
        // Route::resource('room-types', RoomTypeController::class);
        // Route::resource('rooms', RoomController::class);
    });
});