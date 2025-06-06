<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\CabangController;
use App\Http\Controllers\Frontend\BookingController;
use App\Http\Controllers\Frontend\KamarController;

// Halaman utama - daftar cabang
Route::get('/', [CabangController::class, 'index'])->name('cabang.index');

// Detail cabang & daftar kamar
Route::get('/cabang/{id}', [CabangController::class, 'show'])->name('cabang.show');

// ✅ Route untuk ambil gambar cabang dari database (BLOB)
Route::get('/cabang/gambar/{id}', [CabangController::class, 'tampilkanGambar'])->name('cabang.gambar');

// Detail kamar
Route::get('/kamar/{id}', [KamarController::class, 'show'])->name('kamar.show');

// Form booking kamar tertentu
Route::get('/booking/{kamar_id}', [BookingController::class, 'form'])->name('booking.form');

// Proses booking ke Midtrans
Route::post('/booking/payment', [BookingController::class, 'payment'])->name('booking.payment');

// Halaman sukses booking
Route::get('/booking/sukses/{kode_booking}', [BookingController::class, 'success'])->name('booking.success');

// Route tambahan untuk melihat detail booking
Route::get('/booking/{id}', [BookingController::class, 'show'])->name('booking.show');

// Route untuk Midtrans notification (PENTING!)
Route::post('/midtrans/notification', [BookingController::class, 'midtransNotification'])->name('midtrans.notification');

// ✅ FIXED: Add proper route name for create-payment
Route::post('/api/create-payment', [BookingController::class, 'createPayment'])->name('create-payment');

// Guest success page
Route::get('/booking/guest-success/{orderId}', [BookingController::class, 'guestSuccess'])->name('booking.guest-success');

// Check payment status
Route::post('/api/check-payment-status', [BookingController::class, 'checkPaymentStatus'])->name('check-payment-status');


