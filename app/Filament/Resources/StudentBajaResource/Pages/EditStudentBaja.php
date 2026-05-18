<?php

namespace App\Filament\Resources\StudentBajaResource\Pages;

use App\Filament\Resources\StudentBajaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentBaja extends EditRecord
{
    protected static string $resource = StudentBajaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
