<?php

namespace App\Filament\Resources\StudentCardResource\Pages;

use App\Filament\Resources\StudentCardResource;
use Filament\Resources\Pages\ListRecords;

class ListStudentCards extends ListRecords
{
    protected static string $resource = StudentCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            StudentCardResource::createHeaderAction(),
        ];
    }
}
