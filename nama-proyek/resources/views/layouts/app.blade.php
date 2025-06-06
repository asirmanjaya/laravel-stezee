<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penginapan-STeZe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        html {
            scroll-behavior: smooth;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color:rgb(252, 252, 252);
        }

        header {
            background-color: #6b1e11;
            padding: 15px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(235, 228, 228, 0.1);
            position: relative;
            z-index: 10;
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo-container img {
           height: 50px; /* Logo dibesarkan di sini */
            margin-right: 12px;
        }

        .logo-container span {
            font-size: 22px;
            font-weight: bold;
        }

        nav a {
            color: white;
            margin-left: 25px;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            position: relative;
            transition: color 0.3s ease;
        }

        nav a::after {
            content: '';
            position: absolute;
            width: 0%;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: #ffffff;
            transition: width 0.3s ease;
        }

        nav a:hover::after {
            width: 100%;
        }

        .container {
            max-width: 1400px;
            margin: 40px auto 0;
            padding: 40px 20px;
            background-color:rgb(255, 255, 255);
            border-radius: 0 0 15px 15px;
        }

        .container h1 {
            font-size: 22px;
            font-weight: 600;
            text-align: center;
            color: white;
            background-color: #6b1e11;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .grid-cabangs {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
        }

        .card-cabang {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            transition: transform 0.2s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .card-cabang:hover {
            transform: translateY(-5px);
        }

        .card-cabang img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .card-body {
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-body h2 {
            font-size: 18px;
            margin: 8px 0;
            color: #6b1e11;
        }

        .card-footer {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-pilih {
            background-color: #6b1e11;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
        }

        @media (max-width: 1200px) {
            .grid-cabangs {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .grid-cabangs {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 500px) {
            .grid-cabangs {
                grid-template-columns: 1fr;
            }
        }

        .footer {
            background-color: #601c0c;
            color: white;
            padding: 30px 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .footer-left {
            max-width: 400px;
        }

        .footer-left img {
            height: 80px;
            margin-bottom: 10px;
        }

        .footer-icons a {
            color: white;
            margin-right: 15px;
            font-size: 22px;
        }

        .footer-icons a:hover {
            color: #ffcc00;
        }

        .footer-bottom {
            text-align: center;
            width: 100%;
            padding-top: 10px;
            border-top: 1px solid white;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
            <span></span>
        </div>
        <nav>
            <a href="/">Home</a>
            <a href="#footer">Kontak Kami</a> <!-- Scroll langsung ke footer -->
        </nav>
    </header>

    <div class="container">
        @yield('content')
    </div>

    <footer id="footer" class="footer"> <!-- Diberi ID untuk scroll -->
        <div class="footer-left">
            <img src="{{ asset('images/logo.png') }}" alt="STeZe Logo">
            <p><strong>Penginapan STeZe</strong> hadir untuk memberikan kenyamanan terbaik di berbagai lokasi strategis. Nikmati pengalaman menginap yang modern, bersih, dan ramah dompet.</p>
        </div>

        <div>
            <h3>Kontak Kami</h3>
            <p><a href="mailto:stezesipinmarketing@gmail.com" style="color:white; text-decoration: underline;">stezesipinmarketing@gmail.com</a></p>
            <p>+62853 6729 7116</p>
            <p>Jl. Syamsu Bahroen No.12 Selamat, Kec. Telanaipura</p>
            <div class="footer-icons">
                <a href="https://www.instagram.com/stezecollaboration_official?igsh=b2c2MGRha281dGtm" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="https://www.tiktok.com/@namaakun_tiktokmu" target="_blank"><i class="fab fa-tiktok"></i></a>
                <a href="https://www.facebook.com/profile.php?id=100091122925624&mibextid=ZbWKwL" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="https://youtube.com/@stezekostandguesthouse6854?si=MBq4zlRD6aXvfuGu" target="_blank"><i class="fab fa-youtube"></i></a>
            </div>
        </div>

        <div class="footer-bottom">
            &copy; 2025 STeZe. All rights reserved.
        </div>
    </footer>
</body>
</html>
