<?php

namespace App\Filament\Resources\ConsultaAdeudoResource\Pages;

use App\Filament\Resources\ConsultaAdeudoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsultaAdeudos extends ListRecords
{
    protected static string $resource = ConsultaAdeudoResource::class;

    

    protected function canCreate(): bool
    {
        return false;
    }
}
