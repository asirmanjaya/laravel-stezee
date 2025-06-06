<?php

namespace App\Filament\Resepsionis\Widgets;

use App\Models\Pemesanan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class WidgetResepsionis extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $user = auth()->user();
        $cabangId = $user->cabang_id ?? null;

        // Jumlah tamu Check-In Hari Ini
        $checkinToday = Pemesanan::whereDate('tanggal_checkin', $today)
            ->whereHas('kamar', fn($q) => $q->where('cabang_id', $cabangId))
            ->count();

        // Jumlah tamu Check-Out Hari Ini
        $checkoutToday = Pemesanan::whereDate('tanggal_checkout', $today)
            ->whereHas('kamar', fn($q) => $q->where('cabang_id', $cabangId))
            ->count();

        // Total Pemesanan Aktif (tamu sedang menginap)
        $aktifSekarang = Pemesanan::whereDate('tanggal_checkin', '<=', $today)
            ->whereDate('tanggal_checkout', '>', $today)
            ->whereHas('kamar', fn($q) => $q->where('cabang_id', $cabangId))
            ->count();

        return [
            Stat::make('Check-In Hari Ini', $checkinToday),
            Stat::make('Check-Out Hari Ini', $checkoutToday),
            Stat::make('Pemesanan Aktif Sekarang', $aktifSekarang),
        ];
    }
}
