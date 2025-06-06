<?php

namespace App\Filament\Resepsionis\Resources\PemesananResource\Pages;

use App\Filament\Resepsionis\Resources\PemesananResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Form;
use Filament\Forms\Components;

class EditPemesanan extends EditRecord
{
    protected static string $resource = PemesananResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    

    
}
