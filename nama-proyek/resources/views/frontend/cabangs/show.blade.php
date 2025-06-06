@extends('layouts.app')

@section('content')
<style>
    .section {
        padding: 40px 20px;
        background-color:rgb(249, 248, 247);
    }

    .gambar-utama-wrapper {
        width: 100%;
        height: 420px;
        margin-bottom: 30px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .gambar-utama-wrapper img {
        width: 60%;
        height: 100%;
        object-fit: cover;
        border-radius: 12px;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .alamat-cabang {
        font-size: 16px;
        margin-bottom: 5px;
    }

    .harga-box {
        text-align: right;
    }

    .harga-awal {
        font-size: 16px;
        color: #6b1e11;
    }

    .harga-besar {
        font-size: 22px;
        font-weight: bold;
        color: #6b1e11;
    }

    .btn-pilih {
        display: inline-block;
        padding: 8px 16px;
        background-color: #6b1e11;
        color: white;
        text-align: center;
        border-radius: 6px;
        text-decoration: none;
        margin-top: 8px;
    }

    .btn-pilih:hover {
        background-color: #a42c1d;
    }

    .tipe-kamar {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
        margin-top: 20px;
    }

    .kamar-card {
        width: 300px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .kamar-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .kamar-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .kamar-card-body {
        padding: 20px;
    }

    .kamar-card-body h4 {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
        color: #6b1e11;
    }

    .harga-kamar {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .deskripsi-kamar {
        font-size: 14px;
        color: #555;
        margin-bottom: 6px;
    }

    .stok-kamar {
        font-size: 12px;
        color: #999;
        margin-bottom: 10px;
    }

    .kamar-footer {
        display: flex;
        justify-content: flex-end;
    }

    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .harga-box {
            text-align: left;
            margin-top: 10px;
        }

        .gambar-utama-wrapper {
            height: auto;
        }

        .gambar-utama-wrapper img {
            height: auto;
        }
    }
</style>

<div class="section">
    {{-- Gambar Utama --}}
    <div class="gambar-utama-wrapper">
        <img src="{{ $cabang->gambar ? asset('storage/' . $cabang->gambar) : 'https://via.placeholder.com/800x400?text=Cabang' }}" alt="{{ $cabang->nama }}">
    </div>

    {{-- Header --}}
    <div class="header-content">
        <div>
            <h2 style="font-size:28px; font-weight:bold; color:#6b1e11;">{{ $cabang->nama }}</h2>
            <!--<p class="alamat-cabang">{{ $cabang->alamat }}</p>-->
            <p class="alamat-cabang">
                <a href="{{ $cabang->link_maps }}" target="_blank" style="color: #6b1e11; text-decoration: underline;">
                    {{ $cabang->alamat }}
                </a>
            </p>
        </div>
        <div class="harga-box">
            <!--<p class="harga-awal">Mulai dari</p>
            <p class="harga-besar">Rp {{ number_format($cabang->kamars->min('harga_per_malam')) }} <span style="font-size:14px;">/kamar/malam</span></p>
            <a href="#tipe-kamar" class="btn-pilih">Pilih Kamar</a>-->
        </div>
    </div>

    {{-- Tipe Kamar --}}
    <h4 id="tipe-kamar" style="margin-top:20px;">Tipe Kamar</h4>
    <div class="tipe-kamar">
        @foreach($cabang->kamars as $kamar)
            <div class="kamar-card">
                <img src="{{ $kamar->gambar ? asset('storage/' . $kamar->gambar) : 'https://via.placeholder.com/300x180?text=Kamar' }}" alt="{{ $kamar->tipe }}">
                <div class="kamar-card-body">
                    <h4>{{ $kamar->tipe }}</h4>
                    <div class="harga-kamar">Rp{{ number_format($kamar->harga_per_malam) }} /malam</div>
                    <p class="stok-kamar">Tersisa {{ $kamar->stok }} kamar</p>
                    <div class="kamar-footer">
                        <a href="{{ route('kamar.show', $kamar->id) }}" class="btn-pilih">Pilih Kamar</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
