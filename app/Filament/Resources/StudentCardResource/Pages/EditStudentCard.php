<?php

namespace App\Filament\Resources\StudentCardResource\Pages;

use App\Filament\Resources\StudentCardResource;
use Filament\Resources\Pages\EditRecord;

class EditStudentCard extends EditRecord
{
    protected static string $resource = StudentCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            StudentCardResource::deleteHeaderAction(),
        ];
    }
}
