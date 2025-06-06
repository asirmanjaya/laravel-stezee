<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KamarResource\Pages;
use App\Models\Cabang;
use App\Models\Fasilitas;
use App\Models\Kamar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;

class KamarResource extends Resource
{
    protected static ?string $model = Kamar::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Kamar';
    protected static ?string $modelLabel = 'Kamar';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Kamar')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('cabang_id')
                            ->label('Cabang')
                            ->relationship('cabang', 'nama')
                            ->searchable()
                            ->required(),

                        Select::make('tipe')
                            ->label('Tipe Kamar')
                            ->options([
                                'Standard' => 'Standard',
                                'Deluxe' => 'Deluxe',
                                'Suite' => 'Suite',
                                'Family' => 'Family',
                            ])
                            ->required(),

                        TextInput::make('harga_per_malam')
                            ->label('Harga per Malam')
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->required()
                            ->extraAttributes(['min' => '0'])
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state < 0) {
                                    $set('harga_per_malam', 0);
                                }
                            }),


                        TextInput::make('stok')
                            ->label('Jumlah Kamar')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->extraAttributes(['min' => '0'])
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state < 0) {
                                    $set('stok', 0);
                                }
                            }),
                    ]),

                    Textarea::make('deskripsi')
                        ->label('Deskripsi')
                        ->required()
                        ->rows(3),
                ])
                ->columns(1)
                ->collapsible(),

            Section::make('Media & Fasilitas')
                ->schema([
                    FileUpload::make('gambar')
                        ->label('Gambar Kamar')
                        ->image()
                        ->imageEditor()
                        ->maxSize(2048)
                        ->disk('public') // Tambahkan disk
                        ->directory('kamars') // Ganti dari 'gambar/kamar' → 'kamars' saja agar konsisten
                        ->visibility('public')
                        ->nullable(),

                    CheckboxList::make('fasilitas')
                        ->label('Fasilitas')
                        ->relationship('fasilitas', 'nama')
                        ->columns(2),
                ])
                ->columns(1)
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor')
                    ->label('No')
                    ->rowIndex(),

                TextColumn::make('cabang.nama')
                    ->label('Cabang')
                    ->searchable(),

                TextColumn::make('tipe')
                    ->label('Tipe')
                    ->searchable(),

                TextColumn::make('harga_per_malam')
                    ->label('Harga')
                    ->money('IDR', true),

                TextColumn::make('stok')
                    ->sortable()
                    ->label('Stok'),

                ImageColumn::make('gambar')
                    ->label('Gambar')
                    ->height(50)
                    ->disk('public')
                    ->circular(),
            ])
            ->filters([
                SelectFilter::make('cabang_id')
                    ->label('Cabang')
                    ->relationship('cabang', 'nama')
                    ->searchable(),
                SelectFilter::make('tipe')
                    ->label('Tipe Kamar')
                    ->options([
                        'single' => 'Single Room',
                        'double' => 'Double Room',
                        'deluxe' => 'Deluxe Room',
                        'twin_bed' => 'Twin Bed',
                        'family' => 'Family Room',
                    ])
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListKamars::route('/'),
            'create' => Pages\CreateKamar::route('/create'),
            'edit' => Pages\EditKamar::route('/{record}/edit'),
        ];
    }
}
