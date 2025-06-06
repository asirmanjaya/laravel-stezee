<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PemesananResource\Pages;
use App\Filament\Resources\PemesananResource\RelationManagers;
use App\Models\Pemesanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;

class PemesananResource extends Resource
{
    protected static ?string $model = Pemesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor')
                    ->label('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('kamar.cabang.nama')->label('Cabang'),
                Tables\Columns\TextColumn::make('kamar.tipe')->label('Kamar'),
                Tables\Columns\TextColumn::make('sumber')->label('Sumber'),
                Tables\Columns\TextColumn::make('status')->label('Status'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('cabang_id')
                    ->label('Cabang')
                    ->relationship('kamar.cabang', 'nama'), // filter berdasarkan relasi cabang
                SelectFilter::make('kamar_id')
                ->label('Kamar')
                ->relationship('kamar', 'tipe'),

            SelectFilter::make('sumber')
                ->label('Sumber')
                ->options([
                    'online' => 'Online',
                    'walkin' => 'Walk-In',
                ])
                ->searchable(),
            ])
            ->actions([]) // Tidak ada tombol Edit atau Delete
            ->bulkActions([]); // Tidak ada bulk delete
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPemesanans::route('/'),
            'create' => Pages\CreatePemesanan::route('/create'),
            'edit' => Pages\EditPemesanan::route('/{record}/edit'),
        ];
    }
}
