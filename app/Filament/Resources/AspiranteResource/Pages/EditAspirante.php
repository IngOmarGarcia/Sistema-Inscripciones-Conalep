<?php

namespace App\Filament\Resources\AspiranteResource\Pages;

use App\Filament\Resources\AspiranteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAspirante extends EditRecord
{
    protected static string $resource = AspiranteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
