@extends('layouts.app')

@section('content')
    <div class="container text-center mt-5">
        <h2>Konfirmasi Pembayaran</h2>
        <p>Silakan klik tombol di bawah untuk melakukan pembayaran.</p>
        <button id="pay-button" class="btn btn-primary">Bayar Sekarang</button>
    </div>

    {{-- Load Snap SDK Midtrans --}}
    @if(config('midtrans.isProduction'))
        <script src="https://app.midtrans.com/snap/snap.js" 
            data-client-key="{{ config('midtrans.clientKey') }}">
        </script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" 
            data-client-key="{{ config('midtrans.clientKey') }}">
        </script>
    @endif

    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function () {
            // Pastikan snap token ada
            @if(isset($snapToken) && $snapToken)
                snap.pay('{{ $snapToken }}', {
                    onSuccess: function (result) {
                        console.log('Success:', result);
                        alert("Pembayaran sukses!");
                        window.location.href = "{{ route('booking.success', $booking_id) }}";
                    },
                    onPending: function (result) {
                        console.log('Pending:', result);
                        alert("Menunggu pembayaran...");
                        // Optional: redirect ke halaman pending
                        // window.location.href = "{{ route('booking.show', $booking_id) }}";
                    },
                    onError: function (result) {
                        console.error('Error:', result);
                        alert("Pembayaran gagal: " + (result.status_message || 'Terjadi kesalahan'));
                    },
                    onClose: function () {
                        alert("Anda menutup popup tanpa menyelesaikan pembayaran.");
                        // Optional: redirect kembali ke form
                    }
                });
            @else
                alert('Token pembayaran tidak valid. Silakan coba lagi.');
                window.history.back();
            @endif
        }

        // Auto trigger payment jika diinginkan
        // document.getElementById('pay-button').click();
    </script>
@endsection