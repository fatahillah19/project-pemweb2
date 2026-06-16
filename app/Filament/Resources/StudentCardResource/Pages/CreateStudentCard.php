<?php

namespace App\Filament\Resources\StudentCardResource\Pages;

use App\Filament\Resources\StudentCardResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentCard extends CreateRecord
{
    protected static string $resource = StudentCardResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (blank($data['nomor_kartu'] ?? null) && filled($data['id_siswa'] ?? null)) {
            $data['nomor_kartu'] = 'KP' . str_pad((string) $data['id_siswa'], 5, '0', STR_PAD_LEFT);
        }

        if (blank($data['barcode_token'] ?? null) && filled($data['id_siswa'] ?? null)) {
            $data['barcode_token'] = 'SISWA-' . str_pad((string) $data['id_siswa'], 5, '0', STR_PAD_LEFT);
        }

        if (blank($data['qr_token'] ?? null) && filled($data['id_siswa'] ?? null)) {
            $data['qr_token'] = 'QR-SISWA-' . str_pad((string) $data['id_siswa'], 5, '0', STR_PAD_LEFT);
        }

        return $data;
    }
}
