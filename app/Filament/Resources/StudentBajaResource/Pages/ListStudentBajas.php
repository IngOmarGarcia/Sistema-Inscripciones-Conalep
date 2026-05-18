<?php

namespace App\Filament\Resources\StudentBajaResource\Pages;

use App\Filament\Resources\StudentBajaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentBajas extends ListRecords
{
    protected static string $resource = StudentBajaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
