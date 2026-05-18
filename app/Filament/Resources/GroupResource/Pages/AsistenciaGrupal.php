<?php

namespace App\Filament\Resources\GroupResource\Pages;

use App\Filament\Resources\GroupResource;
use Filament\Resources\Pages\Page;
use App\Models\Group;

class AsistenciaGrupal extends Page
{
    protected static string $resource = GroupResource::class;

    protected static string $view = 'filament.resources.group-resource.pages.asistencia-grupal';

    public Group $group;

    public function mount(int|string $record): void
    {
        $this->group = Group::with([
            'students.asistencias',
            'reuniones',
            'asistencias',
        ])->findOrFail($record);
    }
}
