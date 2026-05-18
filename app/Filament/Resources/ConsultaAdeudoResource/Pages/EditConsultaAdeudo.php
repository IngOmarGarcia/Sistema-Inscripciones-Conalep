<?php

namespace App\Filament\Resources\ConsultaAdeudoResource\Pages;

use App\Filament\Resources\ConsultaAdeudoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConsultaAdeudo extends EditRecord
{
    protected static string $resource = ConsultaAdeudoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
