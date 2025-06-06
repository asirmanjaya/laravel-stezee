<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Pemesanan;
use App\Models\Kamar;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function form(Request $request, $kamar_id)
    {
        $kamar = Kamar::findOrFail($kamar_id);
        $harga = $request->input('harga', $kamar->harga_per_malam);
        return view('frontend.booking.form', compact('kamar', 'harga'));
    }

    public function show($id)
    {
        $booking = Pemesanan::findOrFail($id);
        return view('frontend.booking.show', compact('booking'));
    }

    public function success($id)
    {
        $booking = Pemesanan::findOrFail($id);
        return view('frontend.booking.success', compact('booking'));
    }

    /**
     * Handle guest payment (no login required)
     */
    public function createPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|min:3',
                'phone' => 'required|string',
                'tanggal_lahir' => 'required|date|before:today',
                'email' => 'required|email',
                'gender' => 'required|string',
                'checkin' => 'required|date|after_or_equal:today',
                'checkout' => 'required|date|after:checkin',
                'room_type' => 'required|string',
                'total_amount' => 'required|numeric|min:1',
                'duration' => 'required|integer|min:1',
                'price_per_night' => 'required|numeric|min:1',
            ]);

            $orderId = 'BOOKING-' . time() . '-' . rand(1000, 9999);

            $booking = Pemesanan::create([
                'kode_booking' => 'GUEST-' . strtoupper(Str::random(10)),
                'kamar_id' => null,
                'nomor_kamar' => null,
                'user_id' => null,
                'nama_pemesan' => $validated['nama'],
                'tanggal_checkin' => $validated['checkin'],
                'tanggal_checkout' => $validated['checkout'],
                'jumlah_tamu' => 1,
                'nomor_hp' => $validated['phone'],
                'email' => $validated['email'],
                'jenis_kelamin' => $validated['gender'],
                'sumber' => 'Website Guest',
                'status' => 'Menunggu Pembayaran',
                'total_harga' => $validated['total_amount'],
                'order_id' => $orderId,
            ]);

            // Midtrans configuration
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
            Log::error('Midtrans Guest Payment Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pembayaran'
            ], 500);
        }
    }

    /**
     * Booking process for logged-in users
     */
    public function payment(Request $request)
    {
        try {
            $validated = $request->validate([
                'kamar_id' => 'required|exists:kamars,id',
                'nama_pemesan' => 'required|string|min:3',
                'telepon' => 'required|string',
                'email' => 'required|email',
                'tanggal_lahir' => 'required|date|before:today',
                'checkin' => 'required|date|after_or_equal:today',
                'checkout' => 'required|date|after:checkin',
                'jumlah_tamu' => 'required|integer|min:1',
                'total_harga' => 'required|numeric|min:1',
            ]);

            if (!Auth::check()) {
                return $request->ajax()
                    ? response()->json(['success' => false, 'message' => 'Anda harus login untuk melakukan pemesanan.'])
                    : back()->withErrors(['user' => 'Anda harus login untuk melakukan pemesanan.']);
            }

            $kamar = Kamar::findOrFail($validated['kamar_id']);
            $checkin = new \DateTime($validated['checkin']);
            $checkout = new \DateTime($validated['checkout']);
            $jumlahHari = $checkin->diff($checkout)->days;

            if ($jumlahHari < 1) {
                throw new \Exception('Durasi inap harus minimal 1 malam.');
            }

            // Validasi total harga sesuai hitungan server (hindari manipulasi)
            $totalHarga = $kamar->harga_per_malam * $jumlahHari;

            if ($validated['total_harga'] != $totalHarga) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total harga tidak sesuai dengan durasi inap.',
                ], 422);
            }

            // Generate order ID sebelum insert
            $orderId = 'ORDER-' . time() . '-' . rand(1000, 9999);
            Log::info('Validated data sebelum insert Pemesanan:', $validated);
            $booking = Pemesanan::create([
                    'kode_booking' => 'GUEST-' . strtoupper(Str::random(10)),
                    'kamar_id' => null,
                    'nomor_kamar' => null,
                    'user_id' => null,
                    'nama_pemesan' => $validated['nama'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],  // <== ini
                    'tanggal_checkin' => $validated['checkin'],
                    'tanggal_checkout' => $validated['checkout'],
                    'jumlah_tamu' => 1,
                    'nomor_hp' => $validated['phone'],
                    'email' => $validated['email'],
                    'jenis_kelamin' => $validated['gender'],
                    'sumber' => 'Website Guest',
                    'status' => 'Menunggu Pembayaran',
                    'total_harga' => $validated['total_amount'],
                    'order_id' => $orderId,
            ]);

            // Midtrans config
            Config::$serverKey = config('midtrans.serverKey');
            Config::$isProduction = config('midtrans.isProduction', false);
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $totalHarga,
                ],
                'customer_details' => [
                    'first_name' => $validated['nama_pemesan'],
                    'email' => $validated['email'],
                    'phone' => $validated['telepon'],
                ],
                'item_details' => [
                    [
                        'id' => 'kamar-' . $kamar->id,
                        'price' => $kamar->harga_per_malam,
                        'quantity' => $jumlahHari,
                        'name' => $kamar->nama_kamar . ' (' . $jumlahHari . ' malam)',
                    ]
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'snap_token' => $snapToken,
                    'booking_id' => $booking->id,
                    'message' => 'Token pembayaran berhasil dibuat'
                ]);
            }

            return view('frontend.booking.snap', compact('snapToken', 'booking'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            //Log::error('Midtrans Error: ' . $e->getMessage());
            Log::error('Midtrans Error: ' . $e->getMessage(), [
                'params' => $params,
                'booking' => $booking,
            ]);
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage()])
                : back()->withErrors(['midtrans' => 'Terjadi kesalahan saat memproses pembayaran.']);
        }
    }

    /**
     * Midtrans Payment Notification Handler
     */
    public function midtransNotification(Request $request)
    {
        try {
            Config::$serverKey = config('midtrans.serverKey');
            Config::$isProduction = config('midtrans.isProduction');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $notif = new Notification();

            $orderId = $notif->order_id;
            $status = $notif->transaction_status;

            Log::info("Midtrans Notification: OrderID={$orderId}, Status={$status}");

            $booking = Pemesanan::where('order_id', $orderId)->first();

            if (!$booking && preg_match('/^ORDER-(\d+)-/', $orderId, $matches)) {
                $booking = Pemesanan::find($matches[1]);
            }

            if ($booking) {
                switch ($status) {
                    case 'capture':
                    case 'settlement':
                        $booking->status = 'Berhasil';
                        break;
                    case 'pending':
                        $booking->status = 'Menunggu Pembayaran';
                        break;
                    case 'deny':
                    case 'expire':
                    case 'cancel':
                        $booking->status = 'Gagal';
                        break;
                }

                $booking->save();
                Log::info("Booking updated: ID={$booking->id}, Status={$booking->status}");

                if (in_array($status, ['capture', 'settlement'])) {
                    // TODO: Send confirmation email
                    Log::info('Pembayaran sukses untuk booking: ' . $booking->kode_booking);
                }
            } else {
                Log::warning("Booking not found for order_id: {$orderId}");
            }

            return response()->json(['message' => 'Notifikasi diproses'], 200);

        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat memproses notifikasi'], 500);
        }
    }

    /**
     * Show success page for guest booking
     */
    public function guestSuccess($orderId)
    {
        $booking = Pemesanan::where('order_id', $orderId)->firstOrFail();
        return view('frontend.booking.guest-success', compact('booking'));
    }
    
}
