<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    protected $fillable = ['nama'];

    public function kamars()
    {
        return $this->belongsToMany(Kamar::class, 'kamar_fasilitas');
    }
}
