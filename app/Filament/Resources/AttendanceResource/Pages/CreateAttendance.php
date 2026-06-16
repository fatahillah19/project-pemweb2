<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\StudentCard;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->resolveStudentFromBarcode($data);
    }

    private function resolveStudentFromBarcode(array $data): array
    {
        if (filled($data['nomor_kartu'] ?? null)) {
            $card = StudentCard::query()
                ->where('nomor_kartu', $data['nomor_kartu'])
                ->orWhere('barcode_token', $data['nomor_kartu'])
                ->orWhere('qr_token', $data['nomor_kartu'])
                ->first();

            if (! $card) {
                throw ValidationException::withMessages([
                    'nomor_kartu' => 'QR kartu siswa tidak ditemukan.',
                ]);
            }

            $data['id_siswa'] = $card->id_siswa;
            $data['nomor_kartu'] = $card->qr_token ?: $card->barcode_token ?: $card->nomor_kartu;
        }

        if (blank($data['id_siswa'] ?? null)) {
            throw ValidationException::withMessages([
                'id_siswa' => 'Pilih siswa atau scan QR kartu siswa.',
            ]);
        }

        return $data;
    }
}
