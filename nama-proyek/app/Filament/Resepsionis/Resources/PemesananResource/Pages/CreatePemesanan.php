<?php

namespace App\Filament\Resepsionis\Resources\PemesananResource\Pages;

use App\Filament\Resepsionis\Resources\PemesananResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Checkin;

class CreatePemesanan extends CreateRecord
{
    protected static string $resource = PemesananResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl();
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Jika sumber walkin, buat otomatis checkin
        if ($record->sumber === 'walkin') {
            //Checkin::create([
              //  'pemesanan_id' => $record->id,
                //'kamar_id' => $record->kamar_id,
                //'tanggal_checkin' => $record->tanggal_checkin,
                //'jumlah_tamu' => $record->jumlah_tamu,
                //'status' => 'checkin',
                //'user_id' => auth()->id(), // penting
            //]);
        }
    }
}
