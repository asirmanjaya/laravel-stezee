<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\BookingController;  // <-- ini yang kurang

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Jangan pakai prefix '/api' lagi, karena ini sudah di api.php jadi otomatis route-nya akan jadi /api/...
Route::post('/create-payment', [BookingController::class, 'createPayment'])->name('create-payment');
Route::post('/check-payment-status', [BookingController::class, 'checkPaymentStatus'])->name('check-payment-status');
