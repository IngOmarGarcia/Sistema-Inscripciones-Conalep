<?php

namespace App\Filament\Resources\ConsultaAdeudoResource\Pages;

use App\Filament\Resources\ConsultaAdeudoResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageConsultaAdeudos extends ManageRecords
{
    protected static string $resource = ConsultaAdeudoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
