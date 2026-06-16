<?php

namespace App\Filament\Resources\ReportCardResource\Pages;

use App\Filament\Resources\ReportCardResource;
use App\Models\Grade;
use Filament\Resources\Pages\CreateRecord;

class CreateReportCard extends CreateRecord
{
    protected static string $resource = ReportCardResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->fillSummary($data);
    }

    private function fillSummary(array $data): array
    {
        if (blank($data['rata_rata'] ?? null) && filled($data['id_siswa'] ?? null)) {
            $average = Grade::query()
                ->where('id_siswa', $data['id_siswa'])
                ->where('tahun_ajaran', $data['tahun_ajaran'] ?? '2025/2026')
                ->where('semester', $data['semester'] ?? 'Genap')
                ->whereNotNull('nilai')
                ->avg('nilai');

            $data['rata_rata'] = $average === null ? null : round((float) $average, 2);
        }

        if (blank($data['grade'] ?? null)) {
            $data['grade'] = Grade::calculateGrade($data['rata_rata'] === null ? null : (float) $data['rata_rata']);
        }

        return $data;
    }
}
