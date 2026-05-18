<?php

namespace App\Filament\Resources\ReunionResource\Pages;

use App\Filament\Resources\ReunionResource;
use App\Models\Asistencia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Actions;
use Filament\Resources\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;

class RegistrarAsistencias extends Page
{
    use InteractsWithForms;

    protected static string $resource = ReunionResource::class;

    protected static string $view = 'filament.resources.reunion-resource.pages.registrar-asistencias';

    public $record;

    public array $asistencias = [];

    /**
     * MOUNT → cargar alumnos del grupo
     */
    public function mount($record): void
    {
        $this->record = ReunionResource::getModel()::findOrFail($record);

        foreach ($this->record->group->students as $student) {
            $asistencia = $student->asistencias()
                ->where('reunion_id', $this->record->id)
                ->first();

            $this->asistencias[$student->id] = [
                'asistio'       => $asistencia?->asistio ?? false,
                'tutor_nombre'  => $asistencia?->tutor_nombre ?? '',
            ];
        }

        $this->form->fill([
            'asistencias' => $this->asistencias,
        ]);
    }

    /**
     * FORMULARIO DE ASISTENCIAS
     */
    public function form(Form $form): Form
    {
        return $form->schema(
            $this->record->group->students->map(function ($student) {
                return Forms\Components\Section::make($student->nombre)
                    ->schema([
                        Forms\Components\Toggle::make("asistencias.{$student->id}.asistio")
                            ->label('Asistió'),

                        Forms\Components\TextInput::make("asistencias.{$student->id}.tutor_nombre")
                            ->label('Nombre del tutor'),
                    ])
                    ->columns(2);
            })->toArray()
        );
    }

    /**
     * GUARDAR ASISTENCIAS
     */
    public function save(): void
    {
        foreach ($this->asistencias as $studentId => $data) {
            Asistencia::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'reunion_id' => $this->record->id,
                    'group_id'   => $this->record->group_id,
                ],
                [
                    'asistio'      => $data['asistio'] ?? false,
                    'tutor_nombre' => $data['tutor_nombre'] ?? null,
                ]
            );
        }

        $this->notify('success', 'Asistencias guardadas correctamente');
    }

    /**
     * BOTÓN GUARDAR
     */
    
}
