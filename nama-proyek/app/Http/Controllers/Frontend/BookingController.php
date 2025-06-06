<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pemesanan;
use App\Models\Kamar;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class BookingController extends Controller
{
    // Tampilkan form booking
    public function form(Request $request, $kamar_id)
    {
        $kamar = Kamar::findOrFail($kamar_id);
        $harga = $request->input('harga', $kamar->harga_per_malam); // default ke harga dari DB
        return view('frontend.booking.form', compact('kamar', 'harga'));
    }

    // Tampilkan detail booking berdasarkan ID
    public function show($id)
    {
        $booking = Pemesanan::findOrFail($id);
        return view('frontend.booking.show', compact('booking'));
    }

    // Tampilkan sukses
    public function success($id)
    {
        $booking = Pemesanan::findOrFail($id);
        return view('frontend.booking.success', compact('booking'));
    }

    // BARU: Method untuk guest payment (tanpa login)
    public function createPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|min:3',
                'phone' => 'required|string',
                'email' => 'required|email',
                'gender' => 'required|string',
                'checkin' => 'required|date|after_or_equal:today',
                'checkout' => 'required|date|after:checkin',
                'room_type' => 'required|string',
                'total_amount' => 'required|numeric|min:1',
                'duration' => 'required|integer|min:1',
                'price_per_night' => 'required|numeric|min:1',
            ]);

            // Generate order ID unik
            $orderId = 'BOOKING-' . time() . '-' . rand(1000, 9999);

            // Simpan data booking ke database (sebagai guest booking)
            $booking = Pemesanan::create([
                'kode_booking' => 'GUEST-' . strtoupper(uniqid()),
                'kamar_id' => null, // Bisa di-set null untuk guest booking atau cari berdasarkan room_type
                'nomor_kamar' => null,
                'user_id' => null, // NULL karena guest
                'nama_pemesan' => $validated['nama'],
                'tanggal_checkin' => $validated['checkin'],
                'tanggal_checkout' => $validated['checkout'],
                'jumlah_tamu' => 1,
                'nomor_hp' => $validated['phone'],
                'email' => $validated['email'],
                'jenis_kelamin' => $validated['gender'], // Tambah field ini ke migration jika belum ada
                'sumber' => 'Website Guest',
                'status' => 'Menunggu Pembayaran',
                'total_harga' => $validated['total_amount'],
                'order_id' => $orderId, // Simpan order_id untuk tracking
            ]);

            // Konfigurasi Midtrans
            Config::$serverKey = config('midtrans.serverKey');
            Config::$isProduction = config('midtrans.isProduction', false);
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $validated['total_amount'],
                ],
                'customer_details' => [
                    'first_name' => $validated['nama'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                ],
                'item_details' => [
                    [
                        'id' => 'hotel-room-' . time(),
                        'price' => $validated['price_per_night'],
                        'quantity' => $validated['duration'],
                        'name' => $validated['room_type'] . ' (' . $validated['duration'] . ' malam)',
                    ]
                ],
                'enabled_payments' => [
                    'credit_card', 'bca_va', 'bni_va', 'bri_va', 'mandiri_va', 
                    'permata_va', 'other_va', 'gopay', 'shopeepay', 'indomaret', 'alfamart'
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'booking_id' => $booking->id,
                'order_id' => $orderId,
                'message' => 'Token pembayaran berhasil dibuat'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Midtrans Guest Payment Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    // LAMA: Proses booking dengan login (tetap dipertahankan untuk user yang login)
    public function payment(Request $request)
    {
        try {
            $validated = $request->validate([
                'kamar_id' => 'required|exists:kamars,id',
                'nama_pemesan' => 'required|string',
                'telepon' => 'required|string',
                'email' => 'required|email',
                'checkin' => 'required|date',
                'checkout' => 'required|date|after:checkin',
            ]);

            $kamar = Kamar::findOrFail($request->kamar_id);

            $userId = Auth::id();
            if (!$userId) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda harus login untuk melakukan pemesanan.'
                    ]);
                }
                return back()->withErrors(['user' => 'Anda harus login untuk melakukan pemesanan.']);
            }

            // Hitung total harga berdasarkan jumlah hari
            $checkin = new \DateTime($request->checkin);
            $checkout = new \DateTime($request->checkout);
            $jumlahHari = $checkin->diff($checkout)->days;
            $totalHarga = $kamar->harga_per_malam * $jumlahHari;

            $booking = Pemesanan::create([
                'kode_booking' => 'BOOK-' . strtoupper(uniqid()),
                'kamar_id' => $kamar->id,
                'nomor_kamar' => $kamar->nomor ?? null,
                'user_id' => $userId,
                'nama_pemesan' => $request->nama_pemesan,
                'tanggal_checkin' => $request->checkin,
                'tanggal_checkout' => $request->checkout,
                'jumlah_tamu' => 1,
                'nomor_hp' => $request->telepon,
                'email' => $request->email,
                'sumber' => 'Website',
                'status' => 'Menunggu Pembayaran',
                'total_harga' => $totalHarga,
            ]);

            // Konfigurasi Midtrans
            Config::$serverKey = config('midtrans.serverKey');
            Config::$isProduction = config('midtrans.isProduction');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => 'ORDER-' . $booking->id . '-' . time(),
                    'gross_amount' => $totalHarga,
                ],
                'customer_details' => [
                    'first_name' => $booking->nama_pemesan,
                    'email' => $booking->email,
                    'phone' => $booking->nomor_hp,
                ],
                'item_details' => [
                    [
                        'id' => $kamar->id,
                        'price' => $kamar->harga_per_malam,
                        'quantity' => $jumlahHari,
                        'name' => $kamar->nama_kamar . ' (' . $jumlahHari . ' malam)',
                    ]
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            // Jika request AJAX, return JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'snap_token' => $snapToken,
                    'booking_id' => $booking->id,
                    'message' => 'Token pembayaran berhasil dibuat'
                ]);
            }

            // Jika bukan AJAX, return view seperti biasa
            return view('frontend.booking.snap', [
                'snapToken' => $snapToken,
                'booking_id' => $booking->id,
            ]);

        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Midtrans Error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage()
                ]);
            }
            
            return back()->withErrors(['midtrans' => 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.']);
        }
    }

    // Midtrans Notification Handler (Updated untuk handle guest booking)
    public function midtransNotification(Request $request)
    {
        try {
            Config::$serverKey = config('midtrans.serverKey');
            Config::$isProduction = config('midtrans.isProduction');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $notif = new Notification();

            $orderId = $notif->order_id;
            $transactionStatus = $notif->transaction_status;
            
            // Log untuk debugging
            \Log::info('Midtrans Notification: OrderID=' . $orderId . ', Status=' . $transactionStatus);

            // Cari booking berdasarkan order_id (untuk guest booking)
            $booking = Pemesanan::where('order_id', $orderId)->first();

            // Jika tidak ditemukan, coba cara lama (untuk user booking)
            if (!$booking) {
                $orderParts = explode('-', $orderId);
                $bookingId = $orderParts[1] ?? null;
                if ($bookingId) {
                    $booking = Pemesanan::find($bookingId);
                }
            }

            if ($booking) {
                switch ($transactionStatus) {
                    case 'capture':
                    case 'settlement':
                        $booking->status = 'Berhasil';
                        break;
                    case 'pending':
                        $booking->status = 'Menunggu Pembayaran';
                        break;
                    case 'cancel':
                    case 'deny':
                    case 'expire':
                        $booking->status = 'Gagal';
                        break;
                }

                $booking->save();
                
                // Log berhasil update
                \Log::info('Booking status updated: ID=' . $booking->id . ', Status=' . $booking->status);

                // Kirim email konfirmasi jika pembayaran berhasil
                if (in_array($transactionStatus, ['capture', 'settlement'])) {
                    // TODO: Kirim email konfirmasi ke customer
                    \Log::info('Payment successful for booking: ' . $booking->kode_booking);
                }
            } else {
                \Log::warning('Booking not found for order_id: ' . $orderId);
            }

            return response()->json(['message' => 'Notifikasi diproses'], 200);
        } catch (\Exception $e) {
            \Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing notification'], 500);
        }
    }

    // Method untuk success page guest booking
    public function guestSuccess($orderId)
    {
        $booking = Pemesanan::where('order_id', $orderId)->firstOrFail();
        return view('frontend.booking.guest-success', compact('booking'));
    }
}