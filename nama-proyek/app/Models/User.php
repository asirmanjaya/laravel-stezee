<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'cabang_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    // 🔗 Relasi
    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }

    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $user = Auth::user();

        return $user->role === $panel->getId();
    }
}