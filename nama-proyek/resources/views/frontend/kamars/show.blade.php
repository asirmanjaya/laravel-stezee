@extends('layouts.app')

@section('content')
<style>
    .kamar-section {
        padding: 40px 20px;
        background-color:rgb(249, 249, 249);
    }

    .kamar-title {
        font-size: 28px;
        font-weight: bold;
        color: #6b1e11;
    }

    .alamat {
        font-size: 16px;
        margin-bottom: 5px;
    }

    .harga {
        font-size: 20px;
        font-weight: bold;
        color: #6b1e11;
        margin-top: 10px;
    }

    .fasilitas-kamar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin: 30px 0;
    }

    .fasilitas-item {
        background: #fff;
        padding: 10px 15px;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        font-size: 14px;
    }

    .btn-booking {
        display: inline-block;
        padding: 10px 20px;
        background-color: #6b1e11;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        margin-top: 20px;
    }

    .btn-booking:hover {
        background-color: #a42c1d;
    }

    .kamar-images {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 30px;
        height: 300px;
    }

    .kamar-images img {
        width: 60%;
        height: 120%;
        object-fit: cover;
        border-radius: 10px;
    }

    @media (max-width: 768px) {
        .kamar-images {
            height: auto;
        }

        .kamar-images img {
            width: 100%;
            height: auto;
        }
    }
</style>

<div class="kamar-section">
    <div class="kamar-images">
        <img src="{{ $kamar->gambar ? asset('storage/' . $kamar->gambar) : 'https://via.placeholder.com/600x400?text=Gambar+Kamar' }}" alt="Gambar kamar">
    </div>

    <h2 class="kamar-title">{{ $kamar->tipe }}</h2>
    <!--<p class="alamat">{{ $kamar->cabang->alamat }}</p>-->
    
    {{-- Harga per malam --}}
    <p class="harga">Rp {{ number_format($kamar->harga_per_malam, 0, ',', '.') }} / kamar / malam</p>
    <a href="{{ route('booking.form', $kamar->id) }}" class="btn-booking">Pilih Kamar</a>
    <p class="deskripsi-kamar">{{ \Illuminate\Support\Str::limit($kamar->deskripsi, 100) }}</p>
    <h4 style="margin-top: 40px;">Fasilitas</h4>
    <div class="fasilitas-kamar">
        @foreach($kamar->fasilitas as $fasilitas)
            <div class="fasilitas-item">{{ $fasilitas->nama }}</div>
        @endforeach
    </div>
</div>
@endsection
