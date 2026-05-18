<?php

namespace App\Filament\Resources\AspiranteResource\Pages;

use App\Filament\Resources\AspiranteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAspirantes extends ListRecords
{
    protected static string $resource = AspiranteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
