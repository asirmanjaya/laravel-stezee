<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    protected $fillable = ['pemesanan_id', 'user_id', 'waktu_checkin'];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function resepsionis()
    {
        return $this->belongsTo(User::class, foreignKey:'resepsionis.id');
    }

    public function checkout()
    {
        return $this->hasOne(Checkout::class);
    }
}
