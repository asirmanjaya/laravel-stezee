@extends('layouts.app')

@section('content')
    <h2>Detail Booking #{{ $booking->id }}</h2>
    <p>Nama Pemesan: {{ $booking->nama_pemesan }}</p>
    <p>Email: {{ $booking->email }}</p>
    <p>Status: {{ $booking->status }}</p>
    <p>Check-in: {{ $booking->tanggal_checkin }}</p>
    <p>Check-out: {{ $booking->tanggal_checkout }}</p>
    <p>Total bayar: Rp. {{ number_format($booking->kamar->harga, 0, ',', '.') }}</p>
@endsection

