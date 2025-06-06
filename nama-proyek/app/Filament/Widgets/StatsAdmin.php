<?php

namespace App\Filament\Widgets;

use App\Models\Cabang;
use App\Models\Kamar;
use App\Models\Pelanggan;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsAdmin extends BaseWidget
{
    protected int | string | array $columnSpan = 4; // atau 4 jika ingin full width
    protected function getStats(): array
    {
        $today = Carbon::today();

        return [
            Stat::make('Total Cabang', Cabang::count())
                ->description('Jumlah semua cabang')
                ->icon('heroicon-o-building-office')
                ->color('primary'),

            Stat::make('Total Kamar', Kamar::count())
                ->description('Jenis kamar terdaftar')
                ->icon('heroicon-o-home-modern')
                ->color('info'),

            Stat::make('Total Pemesanan', Pemesanan::count())
                ->description('Semua pemesanan hingga hari ini')
                ->icon('heroicon-o-calendar-days')
                ->color('success'),

            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format(
                    Pembayaran::whereDate('created_at', $today)
                        ->where('status', 'success')
                        ->sum('amount'),
                    0, ',', '.'
                ))
                ->description('Dari semua pembayaran sukses')
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Check-In Hari Ini', Pemesanan::whereDate('tanggal_checkin', $today)->count())
                ->description('Jumlah tamu yang check-in')
                ->icon('heroicon-o-arrow-down-circle')
                ->color('warning'),

            Stat::make('Check-Out Hari Ini', Pemesanan::whereDate('tanggal_checkout', $today)->count())
                ->description('Jumlah tamu yang check-out')
                ->icon('heroicon-o-arrow-up-circle')
                ->color('gray'),
        ];
    }
}
