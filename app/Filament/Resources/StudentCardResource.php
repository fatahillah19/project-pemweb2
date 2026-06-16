<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRoleResourceAccess;
use App\Filament\Resources\StudentCardResource\Pages;
use App\Models\StudentCard;
use BackedEnum;
use chillerlan\QRCode\QRCode;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use UnitEnum;

class StudentCardResource extends Resource
{
    use HasRoleResourceAccess;

    protected static ?string $model = StudentCard::class;
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-identification';
    protected static UnitEnum|string|null $navigationGroup = 'Data Master';
    protected static ?string $modelLabel = 'Kartu Siswa';
    protected static ?string $pluralModelLabel = 'Kartu Siswa';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Data Kartu Siswa')
                    ->schema([
                        Forms\Components\Select::make('id_siswa')
                            ->relationship('student', 'nama_siswa')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->label('Siswa'),
                        Forms\Components\TextInput::make('nomor_kartu')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20)
                            ->label('Nomor Kartu'),
                        Forms\Components\TextInput::make('barcode_token')
                            ->unique(ignoreRecord: true)
                            ->maxLength(80)
                            ->label('Token Lama Barcode')
                            ->helperText('Disimpan untuk kompatibilitas data lama.'),
                        Forms\Components\TextInput::make('qr_token')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(80)
                            ->label('Token QR'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_kartu')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('student.nama_siswa')->label('Siswa')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('student.kelas.nama_kelas')->label('Kelas'),
                Tables\Columns\TextColumn::make('nomor_kartu')->label('Nomor Kartu')->searchable(),
                Tables\Columns\TextColumn::make('qr_token')->label('Token QR')->searchable(),
            ])
            ->actions([
                Actions\Action::make('show_qr')
                    ->label('Lihat QR')
                    ->icon('heroicon-m-qr-code')
                    ->modalHeading(fn (StudentCard $record): string => 'QR Kartu ' . ($record->student?->nama_siswa ?? $record->nomor_kartu))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (StudentCard $record): HtmlString => new HtmlString(self::qrHtml($record))),
                ...self::roleAwareTableActions(),
            ])
            ->bulkActions(self::roleAwareBulkActions());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentCards::route('/'),
            'create' => Pages\CreateStudentCard::route('/create'),
            'edit' => Pages\EditStudentCard::route('/{record}/edit'),
        ];
    }

    private static function qrHtml(StudentCard $record): string
    {
        $value = $record->qr_token ?: $record->barcode_token ?: $record->nomor_kartu;
        $qr = (new QRCode)->render($value);

        return '<div style="display:grid;place-items:center;gap:12px;padding:16px"><img src="' . e($qr) . '" alt="QR Code" style="width:240px;height:240px"><div style="font-family:monospace;font-size:14px">' . e($value) . '</div></div>';
    }
}
