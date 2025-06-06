<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Booking Hotel - Pembayaran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg,rgb(255, 255, 255) 0%,rgb(255, 255, 255) 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(45deg, #691F0C, #2E1D12);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            padding: 40px;
        }
        
        .booking-form {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            border: 2px solid #e9ecef;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        
        .radio-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .date-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .payment-summary {
            background: #F5F0EC;
            padding: 30px;
            border-radius: 15px;
            border: 2px solid #E0E0E0;
            height: fit-content;
        }

        .payment-summary img {
    width: 100%;
    max-width: 400px;  /* Maksimal lebar gambar */
    height: auto;      /* Tinggi mengikuti proporsi */
    border-radius: 10px;
    object-fit: cover; /* Supaya gambar ter-crop rapi tanpa distorsi */
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    display: block;
    margin: 0 auto 20px; /* Center dan beri jarak bawah */
}

        
        .room-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #691F0C;
            text-align: center;
            margin: 20px 0;
        }
        
        .pay-button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #691F0C, #2E1D12);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .pay-button:hover {
            transform: translateY(-2px);
            background: linear-gradient(45deg, #2E1D12, #691F0C);
        }
        
        .pay-button:disabled {
            background: #BDBDBD;
            cursor: not-allowed;
            transform: none;
        }
        
        .error {
            color: #e53e3e;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .content {
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 20px;
            }
        }
    </style>
    <!-- Midtrans Snap JS -->
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="YOUR_CLIENT_KEY_HERE"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Detail Pemesanan Hotel</h1>
            <p>Lengkapi data dan lakukan pembayaran</p>
        </div>
        
        <div class="content">
            <div class="booking-form">
                <h2>Detail Pemesanan</h2>

                
                <form id="bookingForm">

                    <div class="form-group">
                        <input type="hidden" name="kamar_id" value="{{ $kamar->id }}">
                        <label for="nama">Nama Lengkap *</label>
                        <input type="text" id="nama" name="nama" required>
                        <div class="error" id="namaError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Nomor Ponsel *</label>
                        <input type="tel" id="phone" name="phone" required>
                        <div class="error" id="phoneError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="tanggal_lahir">Tanggal Lahir *</label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" required>
                        <div class="error" id="tanggalLahirError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Jenis Kelamin *</label>
                        <div class="radio-group">
                            <div class="radio-item">
                                <input type="radio" id="laki" name="gender" value="Laki-laki" required>
                                <label for="laki">Laki-laki</label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" id="perempuan" name="gender" value="Perempuan" required>
                                <label for="perempuan">Perempuan</label>
                            </div>
                        </div>
                        <div class="error" id="genderError"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Alamat Email *</label>
                        <input type="email" id="email" name="email" required>
                        <div class="error" id="emailError"></div>
                    </div>
                    
                    <div class="date-group">
                        <div class="form-group">
                            <label for="checkin">Check-in *</label>
                            <input type="date" id="checkin" name="checkin" required>
                            <div class="error" id="checkinError"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="checkout">Check-out *</label>
                            <input type="date" id="checkout" name="checkout" required>
                            <div class="error" id="checkoutError"></div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="payment-summary">
            <img src="{{ $kamar->gambar ? asset('storage/' . $kamar->gambar) : 'https://via.placeholder.com/600x400?text=Gambar+Kamar' }}" 
                alt="Gambar kamar {{ $kamar->tipe }}" 
                class="room-image">
                
                <h3>Ringkasan Pemesanan</h3>
                <div style="margin: 20px 0;">
                    <p><strong>Kamar:</strong> {{ $kamar->tipe }}</p>
                    <p><strong>Durasi:</strong> <span id="duration">1 malam</span></p>
                    <p><strong>Harga per malam:</strong> Rp. {{ number_format($kamar->harga_per_malam, 0, ',', '.') }}</p>
                </div>
                
                <div class="total-amount">
                    Total: Rp. <span id="totalAmount">150,000</span>
                </div>
                
                <div class="loading" id="loading">
                    <div class="spinner"></div>
                    <p>Memproses pembayaran...</p>
                </div>
                
                <button type="button" class="pay-button" id="payButton">
                    Bayar Sekarang
                </button>
            </div>
        </div>
    </div>

    <script>
        // Configuration
        const CONFIG = {
            baseUrl: 'http://127.0.0.1:8000', // Ganti dengan URL backend Anda
            snapClientKey: 'YOUR_CLIENT_KEY_HERE' // Ganti dengan client key Midtrans Anda
        };

        // Form elements
        const form = document.getElementById('bookingForm');
        const payButton = document.getElementById('payButton');
        const loading = document.getElementById('loading');
        const checkinInput = document.getElementById('checkin');
        const checkoutInput = document.getElementById('checkout');
        const tanggalLahirInput = document.getElementById('tanggal_lahir');
        const durationSpan = document.getElementById('duration');
        const totalAmountSpan = document.getElementById('totalAmount');

        // Set tanggal lahir maksimal 18 tahun yang lalu
        const eighteenYearsAgo = new Date();
        eighteenYearsAgo.setFullYear(eighteenYearsAgo.getFullYear() - 18);
        tanggalLahirInput.max = eighteenYearsAgo.toISOString().split('T')[0];

        // Set checkin hanya bisa hari ini
        const today = new Date().toISOString().split('T')[0];
        checkinInput.min = today;
        checkinInput.max = today;
        checkinInput.value = today; // Set default ke hari ini

        // Set checkout minimal besok
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        checkoutInput.min = tomorrow.toISOString().split('T')[0];
        checkoutInput.value = tomorrow.toISOString().split('T')[0]; // Set default ke besok

        // Calculate duration and total
        function calculateTotal() {
            const checkin = new Date(checkinInput.value);
            const checkout = new Date(checkoutInput.value);

            // Pastikan harga per malam sebagai number, pakai parseFloat
            const pricePerNight = parseFloat(@json($kamar->harga_per_malam));

            if (checkin && checkout && checkout > checkin) {
                const diffTime = Math.abs(checkout - checkin);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                const total = diffDays * pricePerNight;

                durationSpan.textContent = `${diffDays} malam`;
                totalAmountSpan.textContent = total.toLocaleString('id-ID');

                return { days: diffDays, total: total };
            }
            // fallback jika tanggal belum valid, asumsikan 1 malam
            return { days: 1, total: pricePerNight };
        }


        // Event listeners for date changes
        checkoutInput.addEventListener('change', calculateTotal);

        // Form validation
        function validateForm() {
            let isValid = true;
            const errors = {};

            // Clear previous errors
            document.querySelectorAll('.error').forEach(el => el.textContent = '');

            // Validate nama
            const nama = document.getElementById('nama').value.trim();
            if (!nama) {
                errors.nama = 'Nama lengkap harus diisi';
                isValid = false;
            } else if (nama.length < 3) {
                errors.nama = 'Nama minimal 3 karakter';
                isValid = false;
            }

            // Validate phone
            const phone = document.getElementById('phone').value.trim();
            if (!phone) {
                errors.phone = 'Nomor ponsel harus diisi';
                isValid = false;
            } else if (!/^[0-9+\-\s()]{10,15}$/.test(phone)) {
                errors.phone = 'Format nomor ponsel tidak valid';
                isValid = false;
            }

            // Validate tanggal lahir
            const tanggalLahir = tanggalLahirInput.value;
            if (!tanggalLahir) {
                errors.tanggalLahir = 'Tanggal lahir harus diisi';
                isValid = false;
            } else {
                const birthDate = new Date(tanggalLahir);
                const age = Math.floor((new Date() - birthDate) / (365.25 * 24 * 60 * 60 * 1000));
                if (age < 18) {
                    errors.tanggalLahir = 'Usia minimal 18 tahun';
                    isValid = false;
                }
            }

            // Validate gender
            const gender = document.querySelector('input[name="gender"]:checked');
            if (!gender) {
                errors.gender = 'Jenis kelamin harus dipilih';
                isValid = false;
            }

            // Validate email
            const email = document.getElementById('email').value.trim();
            if (!email) {
                errors.email = 'Email harus diisi';
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                errors.email = 'Format email tidak valid';
                isValid = false;
            }

            // Validate dates
            const checkin = checkinInput.value;
            const checkout = checkoutInput.value;
            
            if (!checkin) {
                errors.checkin = 'Tanggal check-in harus diisi';
                isValid = false;
            }

            if (!checkout) {
                errors.checkout = 'Tanggal check-out harus diisi';
                isValid = false;
            } else if (checkout <= checkin) {
                errors.checkout = 'Tanggal check-out harus setelah check-in';
                isValid = false;
            }

            // Display errors
            Object.keys(errors).forEach(field => {
                const errorElement = document.getElementById(field + 'Error');
                if (errorElement) {
                    errorElement.textContent = errors[field];
                }
            });

            return isValid;
        }

        // Get CSRF token
        function getCSRFToken() {
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        }

        // Process payment
        async function processPayment() {
            if (!validateForm()) {
                alert('Mohon lengkapi semua data dengan benar!');
                return;
            }

            // Show loading
            loading.style.display = 'block';
            payButton.disabled = true;

            try {
                // Collect form data
                const formData = new FormData(form);
                const bookingData = {};
                
                formData.forEach((value, key) => {
                    bookingData[key] = value;
                });

                // Add calculated data
                const calculation = calculateTotal();
                bookingData.duration = calculation.days;
                bookingData.total_amount = calculation.total;
                bookingData.price_per_night = parseFloat(@json($kamar->harga_per_malam));
                bookingData.room_type = @json($kamar->tipe);
                bookingData._token = getCSRFToken(); // Add CSRF token

                if (!bookingData.kamar_id) {
                    throw new Error('ID kamar tidak ditemukan!');
                }
                console.log('Sending booking data:', bookingData);

                // Send to backend untuk create snap token
                const response = await fetch(`${CONFIG.baseUrl}/api/create-payment`, { // Fixed endpoint
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCSRFToken(), // Add CSRF header
                    },
                    body: JSON.stringify(bookingData)
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Gagal membuat pembayaran');
                }

                // Hide loading
                loading.style.display = 'none';
                payButton.disabled = false;

                // Open Midtrans Snap
                if (result.snap_token) {
                    window.snap.pay(result.snap_token, {
                        onSuccess: function(result) {
                            alert('Pembayaran berhasil!');
                            console.log('Payment success:', result);
                            // Redirect ke halaman success atau kirim konfirmasi ke server
                            window.location.href = '/booking-success';
                        },
                        onPending: function(result) {
                            alert('Pembayaran pending, silakan selesaikan pembayaran Anda.');
                            console.log('Payment pending:', result);
                        },
                        onError: function(result) {
                            alert('Pembayaran gagal!');
                            console.log('Payment error:', result);
                        },
                        onClose: function() {
                            console.log('Payment popup closed');
                        }
                    });
                } else {
                    throw new Error('Snap token tidak ditemukan');
                }

            } catch (error) {
                console.error('Payment error:', error);
                alert('Terjadi kesalahan: ' + error.message);
                
                // Hide loading
                loading.style.display = 'none';
                payButton.disabled = false;
            }
        }

        // Pay button event listener
        payButton.addEventListener('click', processPayment);

        // Initialize
        calculateTotal();
    </script>
</body>
</html>