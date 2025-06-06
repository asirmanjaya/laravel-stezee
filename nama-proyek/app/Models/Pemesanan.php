<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_booking', 'kamar_id', 'user_id', 'nomor_kamar','nama_pemesan', 'tanggal_lahir',
        'tanggal_checkin', 'tanggal_checkout', 'jumlah_tamu', 'nomor_hp',
        'email', 'sumber', 'status', 'total_harga','metode_pembayaran'
    ];

    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    public function checkin()
    {
        return $this->hasOne(Checkin::class);
    }

    protected static function booted()
    {
        static::creating(function ($pemesanan) {
            if (empty($pemesanan->kode_booking)) {
                $pemesanan->kode_booking = 'BK' . strtoupper(substr(md5(uniqid()), 0, 8));
            }

            if ($pemesanan->kamar && $pemesanan->kamar->stok <= 0) {
                throw new \Exception("Stok kamar habis.");
            }
        });

        static::created(function ($pemesanan) {
            if ($pemesanan->kamar) {
                $pemesanan->kamar->decrement('stok');
            }
        });

        static::deleted(function ($pemesanan) {
            if ($pemesanan->kamar) {
                $pemesanan->kamar->increment('stok');
            }
        });
    }

}
