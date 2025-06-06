<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    protected $fillable = ['nama', 'alamat', 'link_maps', 'gambar'];

    public function kamars()
    {
        return $this->hasMany(Kamar::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
