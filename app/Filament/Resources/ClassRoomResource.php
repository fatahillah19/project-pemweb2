<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRoleResourceAccess;
use App\Filament\Resources\ClassRoomResource\Pages;
use App\Models\ClassRoom;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class ClassRoomResource extends Resource
{
    use HasRoleResourceAccess;

    protected static ?string $model = ClassRoom::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-home-modern';
    protected static UnitEnum|string|null $navigationGroup = 'Data Master';
    protected static ?string $modelLabel = 'Kelas';
    protected static ?string $pluralModelLabel = 'Data Kelas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Visualisasi Kelas')
                    ->schema([
                        Forms\Components\TextInput::make('nama_kelas')
                            ->label('Nama Kelas')
                            ->required()
                            ->maxLength(30)
                            ->placeholder('Contoh: X IPA 1'),
                    ])->columns(2),
                Section::make('Daftar Siswa')
                    ->description('Tambahkan beberapa siswa sekaligus melalui relationship kelas.')
                    ->schema([
                        Forms\Components\Repeater::make('students')
                            ->relationship()
                            ->label('Siswa')
                            ->schema([
                                Forms\Components\TextInput::make('nama_siswa')
                                    ->label('Nama Siswa')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('no_hp')
                                    ->label('No HP')
                                    ->tel()
                                    ->maxLength(20),
                                Forms\Components\FileUpload::make('foto')
                                    ->label('Foto')
                                    ->image()
                                    ->directory('students')
                                    ->disk('public'),
                                Forms\Components\Textarea::make('alamat')
                                    ->maxLength(100)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Tambah Siswa')
                            ->reorderable(false)
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_kelas')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('nama_kelas')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('students_count')
                    ->counts('students')
                    ->label('Jumlah Siswa')
                    ->sortable(),
            ])
            ->actions(self::roleAwareTableActions())
            ->bulkActions(self::roleAwareBulkActions());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassRooms::route('/'),
            'create' => Pages\CreateClassRoom::route('/create'),
            'edit' => Pages\EditClassRoom::route('/{record}/edit'),
        ];
    }
}
