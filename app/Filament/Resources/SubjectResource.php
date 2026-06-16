<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRoleResourceAccess;
use App\Filament\Resources\SubjectResource\Pages;
use App\Models\Subject;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class SubjectResource extends Resource
{
    use HasRoleResourceAccess;

    protected static ?string $model = Subject::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-book-open';
    protected static UnitEnum|string|null $navigationGroup = 'Data Master';
    protected static ?string $modelLabel = 'Mata Pelajaran';
    protected static ?string $pluralModelLabel = 'Mata Pelajaran';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Konfigurasi Kurikulum')
                    ->schema([
                        Forms\Components\TextInput::make('nama_mapel')
                            ->label('Nama Mata Pelajaran')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Contoh: Matematika'),
                        Forms\Components\Select::make('id_guru')
                            ->relationship('guru', 'nama_guru')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label('Guru Pengampu Utama'),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_mapel')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('nama_mapel')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('guru.nama_guru')->label('Guru Pengampu'),
            ])
            ->actions(self::roleAwareTableActions())
            ->bulkActions(self::roleAwareBulkActions());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }
}
