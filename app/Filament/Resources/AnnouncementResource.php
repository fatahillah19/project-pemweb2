<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRoleResourceAccess;
use App\Filament\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class AnnouncementResource extends Resource
{
    use HasRoleResourceAccess;

    protected static ?string $model = Announcement::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-megaphone';
    protected static UnitEnum|string|null $navigationGroup = 'Komunikasi';
    protected static ?string $modelLabel = 'Pengumuman';
    protected static ?string $pluralModelLabel = 'Mading Sekolah (Pengumuman)';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Buat Pengumuman Baru')
                    ->schema([
                        Forms\Components\TextInput::make('judul')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Judul pengumuman akademik harian...'),
                        Forms\Components\DatePicker::make('tanggal')
                            ->default(now())
                            ->required(),
                        Forms\Components\Select::make('target')
                            ->options([
                                'Semua' => 'Semua Kalangan',
                                'Guru' => 'Khusus Majelis Guru',
                                'Siswa' => 'Khusus Siswa-Siswi',
                                'Orang Tua' => 'Khusus Wali Murid',
                            ])->default('Semua')->required(),
                        Forms\Components\FileUpload::make('lampiran')
                            ->directory('announcements-attachments')
                            ->disk('public'),
                        Forms\Components\RichEditor::make('isi')
                            ->required()
                            ->columnSpanFull()
                            ->placeholder('Tuliskan isi edaran resmi di sini...'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('tanggal')->date()->sortable(),
                Tables\Columns\TextColumn::make('target')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Semua' => 'primary',
                        'Guru' => 'success',
                        'Siswa' => 'warning',
                        'Orang Tua' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Dibuat Pada'),
            ])
            ->actions(self::roleAwareTableActions())
            ->bulkActions(self::roleAwareBulkActions());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
