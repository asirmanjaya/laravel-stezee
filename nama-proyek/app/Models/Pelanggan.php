<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $fillable = [
        'nama', 'email', 'password', 'nomor_hp', 'tanggal_lahir',
    ];

    public function pemesanans(): HasMany
    {
        return $this->hasMany(related: Pemesanan::class);
    }
}
