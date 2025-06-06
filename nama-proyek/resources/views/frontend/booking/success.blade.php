@extends('layouts.app')

@section('content')
<style>
    body {
        font-family: 'Arial', sans-serif;
        background: linear-gradient(135deg,rgb(255, 255, 255) 0%,rgb(255, 255, 255) 100%);
        min-height: 100vh;
        padding: 20px;
    }
    
    .success-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 15px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .success-header {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
        padding: 30px;
        text-align: center;
    }
    
    .success-header h1 {
        margin: 0;
        font-size: 2.5em;
        margin-bottom: 10px;
    }
    
    .success-header .checkmark {
        font-size: 4em;
        margin-bottom: 20px;
        display: block;
    }
    
    .booking-details {
        padding: 40px;
    }
    
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }
    
    .detail-section {
        background: #f8f9fa;
        padding: 25px;
        border-radius: 10px;
        border-left: 4px solid #28a745;
    }
    
    .detail-section h3 {
        color: #28a745;
        margin-bottom: 15px;
        font-size: 1.3em;
    }
    
    .detail-item {
        margin-bottom: 12px;
        padding: 8px 0;
        border-bottom: 1px solid #e9ecef;
    }
    
    .detail-item:last-child {
        border-bottom: none;
    }
    
    .detail-label {
        font-weight: bold;
        color: #495057;
        display: inline-block;
        width: 120px;
    }
    
    .detail-value {
        color: #212529;
    }
    
    .booking-code {
        background: #fff3cd;
        border: 2px solid #ffc107;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        margin-bottom: 30px;
    }
    
    .booking-code h2 {
        color: #856404;
        margin: 0;
        font-size: 1.8em;
    }
    
    .booking-code p {
        color: #856404;
        margin: 5px 0 0 0;
        font-size: 0.9em;
    }
    
    .status-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 0.85em;
    }
    
    .status-success {
        background: #d4edda;
        color: #155724;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-failed {
        background: #f8d7da;
        color: #721c24;
    }
    
    .action-buttons {
        text-align: center;
        margin-top: 30px;
        padding-top: 30px;
        border-top: 2px solid #e9ecef;
    }
    
    .btn {
        display: inline-block;
        padding: 12px 30px;
        margin: 0 10px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: bold;
        transition: all 0.3s;
    }
    
    .btn-primary {
        background: linear-gradient(45deg, #007bff, #0056b3);
        color: white;
    }
    
    .btn-secondary {
        background: linear-gradient(45deg, #6c757d, #545b62);
        color: white;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .important-note {
        background: #e7f3ff;
        border-left: 4px solid #007bff;
        padding: 20px;
        margin-top: 30px;
        border-radius: 5px;
    }
    
    .important-note h4 {
        color: #007bff;
        margin-bottom: 10px;
    }
    
    @media (max-width: 768px) {
        .detail-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .success-header h1 {
            font-size: 2em;
        }
        
        .booking-details {
            padding: 20px;
        }
        
        .detail-label {
            width: 100px;
        }
    }
</style>

<div class="success-container">
    <div class="success-header">
        <span class="checkmark">✅</span>
        <h1>Booking Berhasil!</h1>
        <p>Terima kasih atas kepercayaan Anda</p>
    </div>
    
    <div class="booking-details">
        <!-- Kode Booking yang Prominent -->
        <div class="booking-code">
            <h2>{{ $booking->kode_booking ?? 'BOOK-' . $booking->id }}</h2>
            <p>Simpan kode booking ini untuk referensi Anda</p>
        </div>
        
        <div class="detail-grid">
            <!-- Informasi Pemesan -->
            <div class="detail-section">
                <h3>📋 Informasi Pemesan</h3>
                <div class="detail-item">
                    <span class="detail-label">Nama:</span>
                    <span class="detail-value">{{ $booking->nama_pemesan }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $booking->email }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">No. HP:</span>
                    <span class="detail-value">{{ $booking->nomor_hp }}</span>
                </div>
                @if($booking->jenis_kelamin)
                <div class="detail-item">
                    <span class="detail-label">Jenis Kelamin:</span>
                    <span class="detail-value">{{ $booking->jenis_kelamin }}</span>
                </div>
                @endif
                <div class="detail-item">
                    <span class="detail-label">Status:</span>
                    <span class="status-badge 
                        @if($booking->status == 'Berhasil') status-success
                        @elseif($booking->status == 'Menunggu Pembayaran') status-pending
                        @else status-failed
                        @endif">
                        {{ $booking->status }}
                    </span>
                </div>
            </div>
            
            <!-- Detail Booking -->
            <div class="detail-section">
                <h3>🏨 Detail Booking</h3>
                <div class="detail-item">
                    <span class="detail-label">Check-in:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($booking->tanggal_checkin)->format('d M Y') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Check-out:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($booking->tanggal_checkout)->format('d M Y') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Durasi:</span>
                    <span class="detail-value">
                        {{ \Carbon\Carbon::parse($booking->tanggal_checkin)->diffInDays(\Carbon\Carbon::parse($booking->tanggal_checkout)) }} malam
                    </span>
                </div>
                @if($booking->kamar)
                <div class="detail-item">
                    <span class="detail-label">Kamar:</span>
                    <span class="detail-value">{{ $booking->kamar->nama_kamar }}</span>
                </div>
                @if($booking->kamar->nomor)
                <div class="detail-item">
                    <span class="detail-label">No. Kamar:</span>
                    <span class="detail-value">{{ $booking->kamar->nomor }}</span>
                </div>
                @endif
                @else
                <div class="detail-item">
                    <span class="detail-label">Tipe Kamar:</span>
                    <span class="detail-value">Deluxe Room</span>
                </div>
                @endif
                <div class="detail-item">
                    <span class="detail-label">Total Harga:</span>
                    <span class="detail-value" style="font-weight: bold; color: #28a745; font-size: 1.1em;">
                        Rp {{ number_format($booking->total_harga, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Informasi Penting -->
        <div class="important-note">
            <h4>📌 Informasi Penting:</h4>
            <ul>
                <li><strong>Simpan kode booking</strong> untuk check-in di hotel</li>
                <li><strong>Check-in:</strong> 14:00 WIB | <strong>Check-out:</strong> 12:00 WIB</li>
                <li><strong>Konfirmasi pembayaran</strong> akan dikirim ke email Anda</li>
                @if($booking->status == 'Menunggu Pembayaran')
                <li style="color: #856404;"><strong>Harap selesaikan pembayaran</strong> dalam 24 jam</li>
                @endif
            </ul>
        </div>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="/" class="btn btn-primary">Kembali ke Beranda</a>
            @if($booking->user_id)
                <a href="/profile/bookings" class="btn btn-secondary">Lihat Semua Booking</a>
            @endif
            <a href="mailto:{{ $booking->email }}?subject=Konfirmasi Booking {{ $booking->kode_booking ?? 'BOOK-' . $booking->id }}" class="btn btn-secondary">Kirim Email</a>
        </div>
    </div>
</div>

<!-- Auto redirect jika masih pending setelah 30 detik -->
@if($booking->status == 'Menunggu Pembayaran')
<script>
    // Cek status pembayaran setiap 10 detik
    let checkCount = 0;
    const maxChecks = 18; // 3 menit maksimal
    
    const checkPaymentStatus = setInterval(function() {
        checkCount++;
        
        fetch('/api/check-payment-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                booking_id: {{ $booking->id }}
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'Berhasil') {
                location.reload(); // Refresh halaman jika pembayaran berhasil
            } else if (checkCount >= maxChecks) {
                clearInterval(checkPaymentStatus);
                console.log('Stopped checking payment status');
            }
        })
        .catch(error => {
            console.error('Error checking payment status:', error);
            if (checkCount >= maxChecks) {
                clearInterval(checkPaymentStatus);
            }
        });
    }, 10000); // Check setiap 10 detik
</script>
@endif
@endsection