<?php 

namespace App\Filament\Resources;

use App\Filament\Resources\CabangResource\Pages;
use App\Models\Cabang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\FileUpload;

class CabangResource extends Resource
{
    protected static ?string $model = Cabang::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationLabel = 'Cabang';

    protected static ?string $pluralModelLabel = 'Cabang';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detail Cabang')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('nama')
                                ->label('Nama Cabang')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('link_maps')
                                ->label('Link Google Maps')
                                ->url()
                                ->required(),
                        ]),

                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->rows(3),
                    ])
                    ->columns(1),

                Section::make('Gambar')
                    ->schema([
                        Forms\Components\FileUpload::make('gambar')
                                ->label('Gambar Cabang')
                                ->image()
                                ->imagePreviewHeight('150')
                                ->directory('cabangs')
                                ->disk('public') // tambahkan ini
                                ->visibility('public')
                                ->maxSize(2048)
                                ->nullable(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor')
                    ->label('No')
                    ->rowIndex(), // akan otomatis tampilkan nomor urut

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Cabang')
                    ->searchable(),

                Tables\Columns\TextColumn::make('alamat')
                    ->label('Alamat')
                    ->limit(30)
                    ->wrap(),

                Tables\Columns\TextColumn::make('link_maps')
                    ->label('Link Maps')
                    ->url(fn ($record) => $record->link_maps)
                    ->openUrlInNewTab()
                    ->limit(20),

                Tables\Columns\ImageColumn::make('gambar')
                    ->label('Gambar')
                    ->disk('public')
                    ->height(50)
                    ->circular(),
            ])
            ->filters([
                // Tambahkan filter jika perlu
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
            // Jika ingin menambahkan RelationManagers (misalnya kamar di cabang)
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCabangs::route('/'),
            'create' => Pages\CreateCabang::route('/create'),
            'edit' => Pages\EditCabang::route('/{record}/edit'),
        ];
    }
}
