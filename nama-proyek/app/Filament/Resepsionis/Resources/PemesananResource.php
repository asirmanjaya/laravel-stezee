<?php

namespace App\Filament\Resepsionis\Resources;

use App\Filament\Resepsionis\Resources\PemesananResource\Pages;
use App\Models\Pemesanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\SelectColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;

class PemesananResource extends Resource
{
    protected static ?string $model = Pemesanan::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Components\Section::make('Data Pemesan')
                ->schema([
                    Components\TextInput::make('nama_pemesan')
                        ->label('Nama Pemesan')
                        ->required()
                        ->disabled(fn ($livewire) => $livewire instanceof EditRecord),

                    Components\DatePicker::make('tanggal_lahir')
                        ->label('Tanggal Lahir')
                        ->required()
                        ->displayFormat('d-m-Y')
                        ->default(now()->subYears(17)->toDateString())
                        ->maxDate(now()->subYears(17))
                        ->helperText('Minimal umur 17 tahun')
                        ->disabled(fn ($livewire) => $livewire instanceof EditRecord),

                    Components\TextInput::make('nomor_hp')
                        ->label('Nomor HP')
                        ->required()
                        ->disabled(fn ($livewire) => $livewire instanceof EditRecord),

                    Components\Hidden::make('email') // disembunyikan
                        ->default(null),
                ]),

            Components\Section::make('Detail Pemesanan')
                ->schema([
                    Components\Select::make('kamar_id')
                        ->label('Tipe Kamar')
                        ->relationship('kamar', 'tipe', fn (Builder $query) =>
                            $query->where('cabang_id', auth()->user()->cabang_id)
                                  ->where('stok', '>', 0))
                        ->required()
                        ->preload()
                        ->placeholder(null)
                        ->disabled(fn ($livewire) => $livewire instanceof EditRecord),

            Components\DatePicker::make('tanggal_checkin')
                        ->label('Tanggal Check-In')
                        ->default(today())
                        ->required()
                        ->displayFormat('d-m-Y')
                        ->minDate(today()) // Hanya tanggal, bukan waktu
                        ->maxDate(today())
                        ->disabled()
                        ->helperText('Check-In hanya untuk hari ini')
                        ->reactive()
                        ->disabled(fn ($livewire) => $livewire instanceof EditRecord),

            Components\DatePicker::make('tanggal_checkout')
                        ->label('Tanggal Check-Out')
                        ->required()
                        ->default(today()->addDay()) // default besok
                        ->displayFormat('d-m-Y')
                        ->reactive() // Agar minDate diperbarui saat check-in diubah
                        ->minDate(fn (callable $get) =>
                            $get('tanggal_checkin')
                                ? Carbon::parse($get('tanggal_checkin'))->addDay()->startOfDay()
                                : today()->addDay()
                        )
                        ->helperText('Checkout minimal 1 hari setelah check-in')
                        ->rules([
                            fn (callable $get) => function ($attribute, $value, $fail) use ($get) {
                                $checkIn = $get('tanggal_checkin');
                                $checkOut = $value;

                                if (!$checkIn || !$checkOut) return;

                                $checkInDate = Carbon::parse($checkIn)->toDateString();
                                $checkOutDate = Carbon::parse($checkOut)->toDateString();

                                if ($checkOutDate <= $checkInDate) {
                                    $fail('Tanggal check-out harus minimal H+1 dari tanggal check-in.');
                                }
                            }
                        ])
                        ->disabled(fn ($livewire) => $livewire instanceof EditRecord),


                    Components\TextInput::make('jumlah_tamu')
                        ->label('Jumlah Tamu')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(2)
                        ->default(1)
                        ->required(),

                    Components\Select::make('sumber')
                        ->label('Sumber Pemesanan')
                        ->options([
                            'walkin' => 'Walk-in',
                        ])
                        ->default('walkin')
                        ->required()
                        ->disabled(fn ($livewire) => $livewire instanceof EditRecord)
                        ->placeholder(null),
                    Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'belum_bayar' => 'Belum Bayar',
                            'lunas' => 'Lunas',
                            'checkin' => 'Check-In',
                            'checkout' => 'Check-Out',
                        ])
                        ->default('checkin')
                        ->required()
                        ->placeholder(null)
                        ->visible(fn () => true),
                    
                    Components\TextInput::make('nomor_kamar')
                        ->label('Nomor Kamar')
                        ->required()
                        ->placeholder('Masukkan nomor kamar yang diberikan')
                        ->maxLength(10),

                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor')->label('No')->rowIndex(),
                Tables\Columns\TextColumn::make('nomor_kamar')->label('Nomor Kamar')->sortable()->searchable(), 
                Tables\Columns\TextColumn::make('kode_booking')->label('Kode Booking')->searchable(),
                Tables\Columns\TextColumn::make('nama_pemesan')->label('Nama Pemesan')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('tanggal_checkin')->label('Check-In')->date('d-m-Y')->sortable(),
                Tables\Columns\TextColumn::make('tanggal_checkout')->label('Check-Out')->date('d-m-Y'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'belum_bayar' => 'gray',
                        'lunas' => 'success',
                        'checkin' => 'warning',
                        'checkout' => 'primary',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'belum_bayar' => 'Belum Bayar',
                        'lunas' => 'Lunas',
                        'checkin' => 'Check-In',
                        'checkout' => 'Check-Out',
                        default => $state,
                    })
                    ->sortable()
                    ->searchable()
                    ->inline(), // memungkinkan edit langsung di tabel
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'belum_bayar' => 'Belum Bayar',
                        'lunas' => 'Lunas',
                        'checkin' => 'Check-In',
                        'checkout' => 'Check-Out',
                    ]),
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                //Tables\Actions\DeleteAction::make()
                    //->after(fn ($record) => $record->kamar?->increment('stok')), // restore stok
            ])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            #->where('sumber', 'walkin') // hanya tampilkan walk-in
            ->whereHas('kamar', function ($query) {
                $query->where('cabang_id', auth()->user()->cabang_id);
            });
    }

    
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['sumber'] = 'walkin';
        $data['status'] = 'checkin'; // status langsung checkin untuk walkin
        $data['email'] = null;
        $data['tanggal_checkin'] = today()->toDateString();

        return $data;
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
