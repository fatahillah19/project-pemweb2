<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRoleResourceAccess;
use App\Filament\Resources\ReportCardResource\Pages;
use App\Models\ReportCard;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ReportCardResource extends Resource
{
    use HasRoleResourceAccess;

    protected static ?string $model = ReportCard::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static UnitEnum|string|null $navigationGroup = 'Kegiatan Akademik';
    protected static ?string $modelLabel = 'Rapor';
    protected static ?string $pluralModelLabel = 'Rapor Siswa';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Rapor Siswa')
                    ->schema([
                        Forms\Components\Select::make('id_siswa')
                            ->relationship('student', 'nama_siswa')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Siswa'),
                        Forms\Components\TextInput::make('tahun_ajaran')
                            ->required()
                            ->maxLength(20)
                            ->default('2025/2026'),
                        Forms\Components\Select::make('semester')
                            ->options([
                                'Ganjil' => 'Ganjil',
                                'Genap' => 'Genap',
                            ])
                            ->required()
                            ->default('Genap'),
                        Forms\Components\TextInput::make('rata_rata')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->label('Rata-rata'),
                        Forms\Components\TextInput::make('grade')
                            ->maxLength(2)
                            ->label('Grade'),
                        Forms\Components\Textarea::make('catatan')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_rapor')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('student.nama_siswa')->label('Siswa')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('student.kelas.nama_kelas')->label('Kelas'),
                Tables\Columns\TextColumn::make('tahun_ajaran')->sortable(),
                Tables\Columns\TextColumn::make('semester')->sortable(),
                Tables\Columns\TextColumn::make('rata_rata')->label('Rata-rata')->sortable()->alignCenter(),
                Tables\Columns\TextColumn::make('grade')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'A' => 'success',
                        'B' => 'info',
                        'C' => 'warning',
                        'D', 'E' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('catatan')->limit(40),
            ])
            ->filters([
                SelectFilter::make('semester')
                    ->options([
                        'Ganjil' => 'Ganjil',
                        'Genap' => 'Genap',
                    ]),
            ])
            ->actions([
                Actions\Action::make('print_report')
                    ->label('E-Rapor')
                    ->icon('heroicon-m-printer')
                    ->url(fn (ReportCard $record): string => route('report-cards.print', $record))
                    ->openUrlInNewTab(),
                ...self::roleAwareTableActions(),
            ])
            ->bulkActions(self::roleAwareBulkActions());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReportCards::route('/'),
            'create' => Pages\CreateReportCard::route('/create'),
            'edit' => Pages\EditReportCard::route('/{record}/edit'),
        ];
    }
}
