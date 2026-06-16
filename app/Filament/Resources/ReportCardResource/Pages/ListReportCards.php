<?php

namespace App\Filament\Resources\ReportCardResource\Pages;

use App\Filament\Resources\ReportCardResource;
use Filament\Resources\Pages\ListRecords;

class ListReportCards extends ListRecords
{
    protected static string $resource = ReportCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ReportCardResource::createHeaderAction(),
        ];
    }
}
