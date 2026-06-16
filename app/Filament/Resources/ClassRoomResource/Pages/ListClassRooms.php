<?php

namespace App\Filament\Resources\ClassRoomResource\Pages;

use App\Filament\Resources\ClassRoomResource;
use Filament\Resources\Pages\ListRecords;

class ListClassRooms extends ListRecords
{
    protected static string $resource = ClassRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ClassRoomResource::createHeaderAction(),
        ];
    }
}
