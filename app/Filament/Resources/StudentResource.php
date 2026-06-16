<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRoleResourceAccess;
use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
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

class StudentResource extends Resource
{
    use HasRoleResourceAccess;

    protected static ?string $model = Student::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-academic-cap';
    protected static UnitEnum|string|null $navigationGroup = 'Data Master';
    protected static ?string $modelLabel = 'Siswa';
    protected static ?string $pluralModelLabel = 'Data Siswa';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Biodata Siswa')
                    ->schema([
                        Forms\Components\TextInput::make('nama_siswa')
                            ->label('Nama Siswa')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Contoh: Ahmad'),
                        Forms\Components\TextInput::make('nisn')
                            ->label('NISN')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20)
                            ->placeholder('Contoh: 0061234567'),
                        Forms\Components\TextInput::make('no_hp')
                            ->label('No HP')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('Contoh: 081234567890'),
                        Forms\Components\FileUpload::make('foto')
                            ->label('Foto')
                            ->image()
                            ->directory('students')
                            ->disk('public')
                            ->visibility('public')
                            ->imageEditor(),
                        Forms\Components\Textarea::make('alamat')
                            ->maxLength(100)
                            ->placeholder('Contoh: Padang')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('id_kelas')
                            ->label('Kelas')
                            ->relationship('kelas', 'nama_kelas')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_siswa')->label('ID')->sortable(),
                Tables\Columns\ImageColumn::make('foto')->label('Foto')->disk('public')->circular(),
                Tables\Columns\TextColumn::make('nisn')->label('NISN')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nama_siswa')->label('Nama Siswa')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('studentCard.nomor_kartu')->label('No Kartu')->searchable(),
                Tables\Columns\TextColumn::make('no_hp')->label('No HP')->searchable(),
                Tables\Columns\TextColumn::make('kelas.nama_kelas')->sortable(),
                Tables\Columns\TextColumn::make('alamat')->searchable()->limit(40),
            ])
            ->filters([
                SelectFilter::make('id_kelas')
                    ->relationship('kelas', 'nama_kelas')
                    ->label('Filter Kelas'),
            ])
            ->actions(self::roleAwareTableActions())
            ->bulkActions(self::roleAwareBulkActions());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
