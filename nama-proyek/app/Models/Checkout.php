<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    protected $fillable = ['checkin_id', 'waktu_checkout'];

    public function checkin()
    {
        return $this->belongsTo(Checkin::class);
    }
}