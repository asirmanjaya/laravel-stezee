<?php

namespace App\Filament\Resepsionis\Resources;

use App\Filament\Resepsionis\Resources\KamarResource\Pages;
use App\Models\Kamar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KamarResource extends Resource
{
    protected static ?string $model = Kamar::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        // Form tidak diperlukan karena tidak boleh edit
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor')
                    ->label('No')
                    ->rowIndex(), 
                Tables\Columns\TextColumn::make('tipe')->label('Tipe Kamar')->searchable(),
                Tables\Columns\TextColumn::make('harga_per_malam') // jika nama kolom 'harga_per_malam' sesuaikan disini
                    ->label('Harga / Malam')
                    ->money('idr', true),
                Tables\Columns\TextColumn::make('stok')->label('Stok')->sortable(),
            ])
            ->filters([])
            ->actions([]) // Disable semua aksi edit/hapus pada row
            ->bulkActions([]) // Disable bulk actions
            ->headerActions([]) // Disable tombol create
            ->emptyStateActions([]); // Disable tombol create saat data kosong
    }

    public static function getEloquentQuery(): Builder
    {
        // Batasi kamar hanya dari cabang tempat resepsionis bertugas
        return parent::getEloquentQuery()
            ->where('cabang_id', auth()->user()->cabang_id);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKamars::route('/'),
            // Hapus route edit dan create supaya tidak bisa diakses
        ];
    }
}
