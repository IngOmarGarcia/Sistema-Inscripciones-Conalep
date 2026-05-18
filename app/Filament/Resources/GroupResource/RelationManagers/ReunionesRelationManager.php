<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Asistencia;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\View;

class ReunionesRelationManager extends RelationManager
{
    protected static string $relationship = 'reuniones';
    protected static ?string $title = 'Reuniones';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Reunión')
                    ->badge(),

                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Sin asignar'),

                IconColumn::make('activa')
                    ->label('Activa')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\Action::make('configurar')
                    ->label('Configurar')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->modalHeading('Configurar reunión')
                    ->form([
                        Forms\Components\TextInput::make('nombre')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('fecha')
                            ->label('Fecha y hora')
                            ->seconds(false)
                            ->timezone('America/Mexico_City')
                            ->native(false)
                            ->dehydrateStateUsing(fn ($state) =>
                                \Carbon\Carbon::parse($state, 'America/Mexico_City')
                                    ->setTimezone('America/Mexico_City')
                                    ->format('Y-m-d H:i:s')
                            )
                            ->required(),
                
                        Forms\Components\Toggle::make('activa')
                            ->label('Reunión activa'),
                    ])
                    ->action(fn (array $data, $record) => $record->update($data))
                    ->closeModalByClickingAway(false),

                Tables\Actions\Action::make('pasarLista')
                    ->label('Pasar lista')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->activa)
                    ->modalHeading('Pasar lista')
                    ->form([
                        Forms\Components\Repeater::make('asistencias')
                            ->label('Lista de alumnos')
                            ->schema([
                                Forms\Components\Hidden::make('student_id'),

                                Forms\Components\TextInput::make('student_nombre')
                                    ->label('Alumno')
                                    ->disabled(),

                                Forms\Components\Toggle::make('asistio')
                                    ->label('Asistió'),

                                Forms\Components\TextInput::make('tutor_nombre')
                                    ->label('Nombre del tutor')
                                    ->placeholder('Opcional'),
                            ])
                            ->columns(3)
                            ->default(function () {
                                $group = $this->getOwnerRecord();
                                $reunion = $this->getMountedTableActionRecord();

                                // Ya no necesitamos pasar $group al use() porque no usaremos group_id
                                return $group->students->map(function ($student) use ($reunion) {

                                    // Quitamos el ->where('group_id')
                                    $asistencia = Asistencia::where('student_id', $student->id)
                                        ->where('reunion_id', $reunion->id)
                                        ->first();

                                    return [
                                        'student_id'     => $student->id,
                                        'student_nombre' => $student->nombre,
                                        'asistio'        => $asistencia?->asistio ?? false,
                                        'tutor_nombre'   => $asistencia?->tutor_nombre, 
                                    ];
                                })->toArray();
                            })
                    ])
                    ->action(function (array $data, $record) {
                        
                        // Quitamos $group = $this->getOwnerRecord(); porque ya no se usa aquí

                        foreach ($data['asistencias'] as $item) {
                            Asistencia::updateOrCreate(
                                [
                                    'student_id' => $item['student_id'],
                                    // Quitamos 'group_id' => $group->id
                                    'reunion_id' => $record->id,
                                ],
                                [
                                    'asistio' => $item['asistio'],
                                    'tutor_nombre' => $item['asistio']
                                        ? ($item['tutor_nombre'] ?? 'Sin nombre')
                                        : 'No asistió',
                                ]
                            );
                        }
                    })
            ]);
    }
}