<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRoleResourceAccess;
use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\Schedule;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ScheduleResource extends Resource
{
    use HasRoleResourceAccess;

    protected static ?string $model = Schedule::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static UnitEnum|string|null $navigationGroup = 'Kegiatan Akademik';

    protected static ?string $modelLabel = 'Jadwal Pelajaran';

    protected static ?string $pluralModelLabel = 'Jadwal Pelajaran';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Jadwal Pelajaran')
                ->schema([
                    Forms\Components\Select::make('hari')
                        ->options(array_combine(
                            ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                            ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                        ))
                        ->required(),
                    Forms\Components\TimePicker::make('jam_mulai')->seconds(false)->required(),
                    Forms\Components\TimePicker::make('jam_selesai')->seconds(false)->required()->after('jam_mulai'),
                    Forms\Components\Select::make('kelas_id')
                        ->relationship('kelas', 'nama_kelas')
                        ->searchable()->preload()->required()->label('Kelas'),
                    Forms\Components\Select::make('subject_id')
                        ->relationship('subject', 'nama_mapel')
                        ->searchable()->preload()->required()->label('Mata Pelajaran'),
                    Forms\Components\Select::make('guru_id')
                        ->relationship('guru', 'nama_guru')
                        ->searchable()->preload()->required()->label('Guru'),
                    Forms\Components\TextInput::make('ruangan')->maxLength(50),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('hari')
            ->columns([
                Tables\Columns\TextColumn::make('hari')->badge()->sortable(),
                Tables\Columns\TextColumn::make('jam_mulai')->time('H:i')->label('Mulai')->sortable(),
                Tables\Columns\TextColumn::make('jam_selesai')->time('H:i')->label('Selesai'),
                Tables\Columns\TextColumn::make('kelas.nama_kelas')->label('Kelas')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('subject.nama_mapel')->label('Mata Pelajaran')->searchable(),
                Tables\Columns\TextColumn::make('guru.nama_guru')->label('Guru')->searchable(),
                Tables\Columns\TextColumn::make('ruangan'),
            ])
            ->filters([
                SelectFilter::make('hari')->options(array_combine(
                    ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                    ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                )),
                SelectFilter::make('kelas_id')->relationship('kelas', 'nama_kelas')->label('Kelas'),
            ])
            ->actions(self::roleAwareTableActions())
            ->bulkActions(self::roleAwareBulkActions());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }
}
