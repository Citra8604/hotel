<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReservationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. PUBLIC ROUTES (Tanpa Token)
// =========================================================================
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// 🔥 JALUR LUPA PASSWORD WA FONNTE (FULL SINKRON)
Route::post('/forgot-password/check-email', [AuthController::class, 'checkEmail']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Jalur Katalog Kamar & Pengecekan Pesanan Publik
Route::get('/room-types', [RoomTypeController::class, 'index']);
Route::get('/room-types/{id}', [RoomTypeController::class, 'show']);
Route::get('/tipe-kamar', [RoomTypeController::class, 'index']);
Route::get('/rooms/search', [ReservationController::class, 'searchRooms']);
Route::get('/rooms/available-for-booking', [ReservationController::class, 'getAvailableRoomsForBooking']);
Route::post('/pemesanan/cek-publik', [ReservationController::class, 'checkOrder']);


// =========================================================================
// 2. PROTECTED ROUTES (Middleware Sanctum)
// =========================================================================
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth & Profil
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function () { return auth()->user(); });

    // Administrator
    Route::get('/kamar', [RoomController::class, 'index']);
    Route::get('/rooms', [RoomController::class, 'index']);
    Route::post('/kamar', [RoomController::class, 'store']);
    Route::post('/rooms', [RoomController::class, 'store']);
    Route::put('/kamar/{id}', [RoomController::class, 'update']);
    Route::put('/rooms/{id}', [RoomController::class, 'update']);
    Route::delete('/kamar/{id}', [RoomController::class, 'destroy']);
    Route::delete('/rooms/{id}', [RoomController::class, 'destroy']);
    Route::post('/room-types', [RoomTypeController::class, 'store']);
    Route::post('/room-types/{id}', [RoomTypeController::class, 'update']);
    Route::delete('/room-types/{id}', [RoomTypeController::class, 'destroy']);
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::get('/admin/stats', [RoomTypeController::class, 'index']); 
    Route::get('/admin/report', [ReservationController::class, 'report']);

    // Receptionist
    Route::get('/pemesanan', [ReservationController::class, 'index']);
    Route::post('/pemesanan/{id}/checkin', [ReservationController::class, 'updateStatus']);
    Route::post('/pemesanan/{id}/checkout', [ReservationController::class, 'updateStatus']);
    Route::get('/kamar/{id}/checkout-info', [ReservationController::class, 'checkoutInfo']);
    Route::get('/receptionist/daily-report', [ReservationController::class, 'dailyReport']);

    // Tamu
    Route::post('/pemesanan', [ReservationController::class, 'store']);
    Route::get('/pemesanan/saya', [ReservationController::class, 'myReservations']);
    Route::post('/pemesanan/{id}/upload-bukti', [ReservationController::class, 'uploadProof']);
    Route::put('/profile/password', [UserController::class, 'updatePassword']);
    Route::put('/profile/update', [UserController::class, 'updateProfile']);
});