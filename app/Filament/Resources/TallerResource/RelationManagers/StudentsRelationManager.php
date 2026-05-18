<?php

namespace App\Filament\Resources\TallerResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Actions\AttachAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';
    protected static ?string $title = 'Alumnos en este Taller';
    protected static ?string $recordTitleAttribute = 'nombre';

    // 🔹 Formulario (para editar adeudo solo para Taller y Superadmin)
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('debe_material')
                    ->label('¿Tiene adeudo?')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state === false) {
                            $set('observaciones', null);
                        }
                    })
                    ->dehydrated()
                    ->onColor('danger')
                    ->offColor('success'),

                Forms\Components\Textarea::make('observaciones')
                    ->label('¿Qué debe el alumno?')
                    ->rows(3)
                    ->columnSpanFull()
                    ->visible(fn (callable $get) => $get('debe_material') === true)
                    ->required(fn (callable $get) => $get('debe_material') === true)
                    ->dehydrated(fn (callable $get) => $get('debe_material') === true),
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['debe_material'] === false) {
            $data['observaciones'] = null;
        }
        return $data;
    }

    // 🔹 Tabla de alumnos
    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('career'))
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('apellido_paterno')->label('Apellido Paterno'),
                Tables\Columns\TextColumn::make('group.career.name')->label('Carrera')->badge()->color('primary'),
                Tables\Columns\IconColumn::make('pivot.debe_material')
                    ->label('¿Tiene adeudo?')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success'),
                Tables\Columns\TextColumn::make('pivot.observaciones')->label('Observaciones')->limit(30),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('adeudo')
                    ->label('Adeudo')
                    ->trueLabel('Con adeudo')
                    ->falseLabel('Sin adeudo')
                    ->queries(
                        true: fn ($query) => $query->where('alumno_taller.debe_material', true),
                        false: fn ($query) => $query->where('alumno_taller.debe_material', false),
                    ),
            ])
            ->headerActions([
    // Botón informativo de estadísticas
    Tables\Actions\Action::make('estadisticas')
        ->label(function () {
            $taller = $this->getOwnerRecord(); // Taller actual
            $total = $taller->students()->count();
            $deben = $taller->students()
                ->where('alumno_taller.debe_material', true)
                ->count();
            $noDeben = $total - $deben;
            return "Total: $total | Con adeudo: $deben | Al corriente: $noDeben";
        })
        ->disabled(), // Solo informativo, no clicable

                AttachAction::make()
                    ->recordTitle(fn ($record) => $record->nombre_completo)
                    ->preloadRecordSelect(false)
                    ->recordSelect(function ($select) {
                        return $select
                            ->searchable(['nombre', 'apellido_paterno', 'apellido_materno', 'matricula'])
                            ->placeholder('Buscar por nombre, apellidos o matrícula...')
                            ->helperText('Solo se muestran alumnos que aún no están inscritos en este taller.')
                            ->noSearchResultsMessage('No se encontraron resultados.');
                    })
                    ->before(function (AttachAction $action, $livewire, array $data) {
                        $taller = $livewire->getOwnerRecord();
                        $studentId = $data['recordId'];
                        $exists = DB::table('alumno_taller')->where('alumno_id', $studentId)->exists();
                        if ($exists) {
                            Notification::make()
                                ->title('Alumno ya asignado')
                                ->body('Este alumno ya pertenece a un taller.')
                                ->danger()
                                ->send();
                            $action->cancel();
                        }
                    })
                    ->visible(fn () => auth()->user()->hasAnyRole(['superadmin','Taller'])),
            ])
            ->actions([
    Tables\Actions\ViewAction::make()
        ->label('Ver Expediente')
        ->visible(fn () => auth()->user()->hasAnyRole(['Taller','escolares'])),

    Tables\Actions\EditAction::make()
        ->mutateFormDataUsing(function (array $data) {
            if (($data['debe_material'] ?? false) === false) {
                $data['observaciones'] = null;
            }
            return $data;
        })
        ->visible(fn () => auth()->user()->hasAnyRole(['superadmin','Taller'])),

    Tables\Actions\DetachAction::make()
        ->visible(fn () => auth()->user()->hasAnyRole(['superadmin','Taller'])),

    
])
            ->bulkActions([
    Tables\Actions\DetachBulkAction::make()
        ->label('Desvincular seleccionados')
        ->visible(fn () => auth()->user()->hasAnyRole(['superadmin','Taller'])),

    Tables\Actions\BulkAction::make('marcarAdeudo')
        ->label('Marcar adeudo')
        ->form([
            Forms\Components\Textarea::make('observaciones')
                ->label('¿Qué deben los alumnos?')
                ->required(),
        ])
        ->action(function ($records, $data) {
            $taller = $this->ownerRecord;
            foreach ($records as $record) {
                $taller->students()->updateExistingPivot(
                    $record->id,
                    [
                        'debe_material' => true,
                        'observaciones' => $data['observaciones'],
                    ]
                );
            }
        })
        ->visible(fn () => auth()->user()->hasAnyRole(['superadmin','Taller'])),

    Tables\Actions\BulkAction::make('quitarAdeudo')
        ->label('Marcar al corriente')
        ->action(function ($records) {
            $taller = $this->ownerRecord;
            foreach ($records as $record) {
                $taller->students()->updateExistingPivot(
                    $record->id,
                    [
                        'debe_material' => false,
                        'observaciones' => null,
                    ]
                );
            }
        })
        ->visible(fn () => auth()->user()->hasAnyRole(['superadmin','Taller'])),
]);
    }

    // 🔹 Permisos para ver alumnos
    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['superadmin','Taller','escolares']);
    }

    public static function canViewForRecord($ownerRecord, $pageClass): bool
    {
        return auth()->user()->hasAnyRole(['superadmin','Taller','escolares']);
    }
}