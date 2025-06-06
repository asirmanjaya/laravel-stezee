<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    // Gunakan tabel 'pemesanans' bukan 'bookings'
    protected $table = 'pemesanans';

    protected $fillable = [
        'pelanggan_id',
        'kamar_id',
        'tanggal_checkin',
        'tanggal_checkout',
        'jumlah_kamar',
        'status',
    ];

    // (Opsional) Jika relasi dengan kamar atau pelanggan sudah ada:
    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }
}
