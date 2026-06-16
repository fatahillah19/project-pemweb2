<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRoleResourceAccess;
use App\Filament\Resources\GradeResource\Pages;
use App\Models\Grade;
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

class GradeResource extends Resource
{
    use HasRoleResourceAccess;

    protected static ?string $model = Grade::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static UnitEnum|string|null $navigationGroup = 'Kegiatan Akademik';
    protected static ?string $modelLabel = 'Nilai';
    protected static ?string $pluralModelLabel = 'Input Nilai Siswa';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Pencatatan Capaian Akademik')
                    ->schema([
                        Forms\Components\Select::make('id_siswa')
                            ->relationship('student', 'nama_siswa')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Siswa'),
                        Forms\Components\Select::make('id_mapel')
                            ->relationship('subject', 'nama_mapel')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Mata Pelajaran'),
                        Forms\Components\TextInput::make('tahun_ajaran')
                            ->required()
                            ->maxLength(20)
                            ->default('2025/2026')
                            ->label('Tahun Ajaran'),
                        Forms\Components\Select::make('semester')
                            ->options([
                                'Ganjil' => 'Ganjil',
                                'Genap' => 'Genap',
                            ])
                            ->required()
                            ->default('Genap'),
                        Forms\Components\TextInput::make('nilai')
                            ->disabled()
                            ->dehydrated(false)
                            ->label('Nilai Akhir')
                            ->helperText('Otomatis dari rata-rata UTS dan UAS.'),
                        Forms\Components\TextInput::make('nilai_uts')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required()
                            ->label('Nilai UTS'),
                        Forms\Components\TextInput::make('nilai_uas')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required()
                            ->label('Nilai UAS'),
                        Forms\Components\TextInput::make('grade')
                            ->disabled()
                            ->dehydrated(false)
                            ->label('Grade')
                            ->helperText('Otomatis dari nilai akhir.'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_nilai')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('student.nama_siswa')->searchable()->sortable()->label('Siswa'),
                Tables\Columns\TextColumn::make('subject.nama_mapel')->sortable()->label('Mata Pelajaran'),
                Tables\Columns\TextColumn::make('tahun_ajaran')->label('Tahun')->sortable(),
                Tables\Columns\TextColumn::make('semester')->sortable(),
                Tables\Columns\TextColumn::make('nilai_uts')->label('UTS')->sortable()->alignCenter(),
                Tables\Columns\TextColumn::make('nilai_uas')->label('UAS')->sortable()->alignCenter(),
                Tables\Columns\TextColumn::make('nilai')->label('Nilai Akhir')->sortable()->alignCenter(),
                Tables\Columns\TextColumn::make('grade')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'A' => 'success',
                        'B' => 'info',
                        'C' => 'warning',
                        'D', 'E' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('id_mapel')
                    ->relationship('subject', 'nama_mapel')
                    ->label('Filter Mata Pelajaran'),
                SelectFilter::make('semester')
                    ->options([
                        'Ganjil' => 'Ganjil',
                        'Genap' => 'Genap',
                    ]),
            ])
            ->actions(self::roleAwareTableActions())
            ->bulkActions(self::roleAwareBulkActions());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGrades::route('/'),
            'create' => Pages\CreateGrade::route('/create'),
            'edit' => Pages\EditGrade::route('/{record}/edit'),
        ];
    }
}
