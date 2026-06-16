<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRoleResourceAccess;
use App\Filament\Resources\TeacherResource\Pages;
use App\Models\Teacher;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class TeacherResource extends Resource
{
    use HasRoleResourceAccess;

    protected static ?string $model = Teacher::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-users';
    protected static UnitEnum|string|null $navigationGroup = 'Data Master';
    protected static ?string $modelLabel = 'Pengajar';
    protected static ?string $pluralModelLabel = 'Data Pengajar';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Biodata Guru')
                    ->schema([
                        Forms\Components\TextInput::make('nama_guru')
                            ->label('Nama Guru')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Contoh: Andi'),
                        Forms\Components\TextInput::make('nip')
                            ->label('NIP')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(30)
                            ->placeholder('Contoh: 198701012010011001'),
                        Forms\Components\TextInput::make('no_hp')
                            ->label('No HP')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('Contoh: 081234567890'),
                        Forms\Components\FileUpload::make('foto')
                            ->label('Foto Pengajar')
                            ->image()
                            ->directory('teachers')
                            ->disk('public')
                            ->visibility('public')
                            ->imageEditor(),
                        Forms\Components\TextInput::make('mapel_diampu')
                            ->label('Mapel yang Diajar')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Contoh: Matematika'),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_guru')->label('ID')->sortable(),
                Tables\Columns\ImageColumn::make('foto')->label('Foto')->disk('public')->circular(),
                Tables\Columns\TextColumn::make('nip')->label('NIP')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nama_guru')->label('Nama Guru')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('no_hp')->label('No HP')->searchable(),
                Tables\Columns\TextColumn::make('mapel_diampu')->label('Mapel yang Diajar')->searchable()->sortable(),
            ])
            ->actions(self::roleAwareTableActions())
            ->bulkActions(self::roleAwareBulkActions());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}
