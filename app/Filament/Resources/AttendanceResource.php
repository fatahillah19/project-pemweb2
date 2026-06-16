<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRoleResourceAccess;
use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use UnitEnum;

class AttendanceResource extends Resource
{
    use HasRoleResourceAccess;

    protected static ?string $model = Attendance::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-check-circle';
    protected static UnitEnum|string|null $navigationGroup = 'Kegiatan Akademik';
    protected static ?string $modelLabel = 'Presensi';
    protected static ?string $pluralModelLabel = 'Presensi Kehadiran';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Pencatatan Absensi Harian')
                    ->schema([
                        Forms\Components\Select::make('id_siswa')
                            ->relationship('student', 'nama_siswa')
                            ->searchable()
                            ->preload()
                            ->label('Siswa'),
                        Forms\Components\TextInput::make('nomor_kartu')
                            ->label('Scan / Token QR Kartu')
                            ->placeholder('Contoh: QR-SISWA-00001')
                            ->helperText('Isi dari QR kartu siswa. Sistem akan mencocokkan siswa saat disimpan.'),
                        Forms\Components\DatePicker::make('tanggal')
                            ->default(now())
                            ->required()
                            ->label('Tanggal Presensi'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Hadir' => 'Hadir',
                                'Izin' => 'Izin',
                                'Sakit' => 'Sakit',
                                'Alpa' => 'Alpa',
                            ])->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_absensi')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('student.nama_siswa')->searchable()->sortable()->label('Siswa'),
                Tables\Columns\TextColumn::make('student.kelas.nama_kelas')->label('Kelas'),
                Tables\Columns\TextColumn::make('nomor_kartu')->label('Token QR')->searchable(),
                Tables\Columns\TextColumn::make('tanggal')->date()->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Izin' => 'warning',
                        'Sakit' => 'info',
                        'Alpa' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Izin' => 'Izin',
                        'Sakit' => 'Sakit',
                        'Alpa' => 'Alpa',
                    ]),
            ])
            ->actions(self::roleAwareTableActions())
            ->bulkActions(self::roleAwareBulkActions());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
