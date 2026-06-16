<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRoleResourceAccess;
use App\Filament\Resources\DepartmentResource\Pages;
use App\Models\Department;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class DepartmentResource extends Resource
{
    use HasRoleResourceAccess;

    // Model harus bertipe ?string
    protected static ?string $model = Department::class;

    // Ikon navigasi juga ?string
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-briefcase';

    // navigationGroup harus UnitEnum|string|null
    protected static UnitEnum|string|null $navigationGroup = 'Data Master';

    // Label model tunggal & jamak harus ?string
    protected static ?string $modelLabel = 'Jurusan';
    protected static ?string $pluralModelLabel = 'Kompetensi Keahlian';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Struktur Jurusan')
                    ->schema([
                        Forms\Components\TextInput::make('kode_jurusan')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->label('Kode Jurusan (Alias)')
                            ->placeholder('Contoh: RPL, TKJ, TBSM'),
                        Forms\Components\TextInput::make('nama_jurusan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Rekayasa Perangkat Lunak'),
                        Forms\Components\Textarea::make('deskripsi')
                            ->columnSpanFull()
                            ->placeholder('Deskripsi kompetensi keahlian dan visi misi lulusan...'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_jurusan')
                    ->badge()
                    ->color('primary')
                    ->label('Kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_jurusan')
                    ->label('Kompetensi Keahlian')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->limit(60)
                    ->label('Deskripsi Singkat'),
            ])
            ->actions(self::roleAwareTableActions())
            ->bulkActions(self::roleAwareBulkActions());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
