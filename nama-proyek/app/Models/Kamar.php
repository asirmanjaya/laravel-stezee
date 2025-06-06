<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    protected $fillable = [
        'cabang_id', 'tipe', 'deskripsi', 'harga_per_malam', 'stok', 'gambar',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function fasilitas()
    {
        return $this->belongsToMany(Fasilitas::class, 'kamar_fasilitas');
    }

    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }

    public function tersediaPada($startDate, $endDate): bool
    {
        $jumlahTerpakai = $this->pemesanans()
            ->whereIn('status', ['lunas', 'checkin']) // booking valid
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where('tanggal_checkin', '<', $endDate)
                ->where('tanggal_checkout', '>', $startDate);
            })
            ->count();

        return $this->stok > $jumlahTerpakai;
    }

}

