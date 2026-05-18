<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GraduatedStudentResource\Pages;
use App\Models\GraduatedStudent;
use App\Models\Student; 
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group; 
use Illuminate\Support\Facades\DB; 
use Filament\Notifications\Notification; 
use Filament\Support\Enums\FontWeight;

class GraduatedStudentResource extends Resource
{
    protected static ?string $model = GraduatedStudent::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Alumnos Graduados';
    protected static ?string $navigationGroup = 'Control Escolar';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'alumnos-graduados';

    const COSTO_INSCRIPCION = 1100;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Expediente de Graduación')
                    ->tabs([
                        // --- PESTAÑA 1: ACADÉMICO ---
                        Forms\Components\Tabs\Tab::make('Académico')
                            ->schema([
                                Forms\Components\TextInput::make('matricula')->label('Matrícula')->disabled(),
                                Forms\Components\Select::make('group_id')->label('Último Grupo')->relationship('group', 'name')->disabled(),
                                Forms\Components\TextInput::make('periodo_egreso')->label('Periodo de Egreso'),
                                Forms\Components\TextInput::make('prom_gral')->label('Promedio Final')->numeric(),
                                Forms\Components\TextInput::make('folio_certificacion')->label('Folio de Certificado'),
                                Forms\Components\TextInput::make('monto_pagado')->label('Acumulado Inscripción ($)')->numeric()->prefix('$'),
                                Forms\Components\TextInput::make('modelo_educativo'),
                                Forms\Components\TextInput::make('plan_estudios'),
                            ])->columns(3),

                        // --- PESTAÑA 2: PERSONALES ---
                        Forms\Components\Tabs\Tab::make('Personales')
                            ->schema([
                                Forms\Components\TextInput::make('nombre')->required(),
                                Forms\Components\TextInput::make('apellido_paterno')->required(),
                                Forms\Components\TextInput::make('apellido_materno'),
                                Forms\Components\TextInput::make('curp')->label('CURP')->length(18),
                                Forms\Components\Select::make('sexo')->options(['M' => 'Masculino', 'F' => 'Femenino']),
                                Forms\Components\DatePicker::make('fecha_nacimiento'),
                                Forms\Components\TextInput::make('email')->label('Correo de Contacto')->email(),
                                Forms\Components\TextInput::make('telefono')->tel(),
                                Forms\Components\TextInput::make('tel_celular')->tel(),
                            ])->columns(3),

                        // --- PESTAÑA 3: HISTORIAL DE PAGOS ---
                        Forms\Components\Tabs\Tab::make('Historial de Pagos')
                            ->icon('heroicon-o-receipt-percent')
                            ->schema([
                                Forms\Components\Repeater::make('pagos')
                                    ->relationship('pagos') 
                                    ->schema([
                                        Forms\Components\Grid::make(4)->schema([
                                            Forms\Components\TextInput::make('folio')->label('Folio')->readOnly(),
                                            Forms\Components\TextInput::make('periodo')->label('Periodo')->readOnly(),
                                            Forms\Components\TextInput::make('monto_total')->label('Total Recibo')->prefix('$')->readOnly(),
                                            
                                            // REGRESAMOS A TU VISTA PERSONALIZADA PARA CONSERVAR EL BOTÓN
                                            Forms\Components\ViewField::make('pdf_path')
                                                ->label('Acción')
                                                ->view('filament.forms.components.download-pdf-link'),
                                        ]),
                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TagsInput::make('conceptos')
                                                ->label('Conceptos Incluidos')
                                                ->disabled(), 
                                            Forms\Components\TextInput::make('abono_inscripcion')
                                                ->label('Abonado a Inscripción')
                                                ->prefix('$')
                                                ->readOnly()
                                                ->extraInputAttributes(['class' => 'text-success-600 font-bold']),
                                        ]),
                                    ])
                                    ->itemLabel(fn (array $state): ?string => "Recibo Folio: " . ($state['folio'] ?? 'S/N'))
                                    ->collapsible()
                                    ->collapsed()
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                            ]),

                        // --- PESTAÑA 4: DOMICILIO ---
                        Forms\Components\Tabs\Tab::make('Domicilio')
                            ->schema([
                                Forms\Components\TextInput::make('calle'),
                                Forms\Components\TextInput::make('dom_colonia')->label('Colonia'),
                                Forms\Components\TextInput::make('dom_cp')->label('C.P.'),
                                Forms\Components\TextInput::make('dom_ciudad')->label('Ciudad'),
                                Forms\Components\TextInput::make('municipio'),
                                Forms\Components\TextInput::make('dom_ent_fed')->label('Estado'),
                            ])->columns(2),

                        // --- PESTAÑA 5: MÉDICOS ---
                        Forms\Components\Tabs\Tab::make('Médicos')
                            ->schema([
                                Forms\Components\TextInput::make('medico_institucion_medica')->label('Institución'),
                                Forms\Components\TextInput::make('medico_cv_filiacion')->label('No. Filiación'),
                                Forms\Components\TextInput::make('medico_alergia')->label('Alergias'),
                                Forms\Components\TextInput::make('medico_cardiopatia')->label('Cardiopatías'),
                                Forms\Components\TextInput::make('medico_epilepsia')->label('Epilepsia'),
                            ])->columns(2),

                        // --- PESTAÑA 6: TUTOR ---
                        Forms\Components\Tabs\Tab::make('Tutor')
                            ->schema([
                                Forms\Components\TextInput::make('tutor_nombre')->label('Nombre del Tutor'),
                                Forms\Components\TextInput::make('tutor_correo')->label('Correo Tutor'),
                                Forms\Components\TextInput::make('tutor_telefono')->label('Teléfono Tutor'),
                                Forms\Components\TextInput::make('tutor_celular')->label('Celular Tutor'),
                            ])->columns(2),
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup(
                Group::make('periodo_egreso')
                    ->label('Periodo Egreso')
                    ->collapsible()
            )
            ->defaultSort('apellido_paterno', 'asc')
            ->columns([
                
                // 1. NOMBRE COMPLETO EN LA MISMA LÍNEA
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Estudiante Egresado')
                    ->getStateUsing(fn (GraduatedStudent $record): string => trim("{$record->nombre} {$record->apellido_paterno} {$record->apellido_materno}"))
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-o-academic-cap')
                    ->color('primary')
                    ->description(fn (GraduatedStudent $record): string => "Matrícula: {$record->matricula}")
                    ->searchable(['nombre', 'apellido_paterno', 'apellido_materno', 'matricula']), 

                Tables\Columns\TextColumn::make('group.name')
                    ->badge()
                    ->color('gray')
                    ->label('Grupo Egreso')
                    ->searchable(),

                // 2. CICLO CON ESTILO UNIFORME
                Tables\Columns\TextColumn::make('periodo_egreso')
                    ->label('Ciclo')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('estatus_pago')
                    ->label('Estatus Adm.')
                    ->badge()
                    ->getStateUsing(function (GraduatedStudent $record) {
                        $pagado = $record->monto_pagado ?? 0;
                        return $pagado >= self::COSTO_INSCRIPCION ? 'Solvente' : 'Con Adeudo';
                    })
                    ->color(fn (string $state): string => $state === 'Solvente' ? 'success' : 'danger')
                    ->icon(fn (string $state): string => $state === 'Solvente' ? 'heroicon-m-check-badge' : 'heroicon-m-exclamation-triangle'),
            ])
            ->filters([
                SelectFilter::make('group_id')
                    ->label('Filtrar por Grupo')
                    ->relationship('group', 'name')
                    ->preload(),

                SelectFilter::make('periodo_egreso')
                    ->label('Ciclo Escolar')
                    ->options(fn () => GraduatedStudent::query()
                        ->whereNotNull('periodo_egreso')
                        ->pluck('periodo_egreso', 'periodo_egreso')
                        ->unique()
                        ->toArray()
                    ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('reactivar')
                        ->label('Mover a Activos')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Reactivar Alumno')
                        ->modalDescription('El alumno volverá a estar activo y recuperará todo su historial.')
                        ->action(function (GraduatedStudent $record) {
                            try {
                                DB::transaction(function () use ($record) {
                                    $student = Student::withTrashed()
                                        ->where('matricula', $record->matricula)
                                        ->first();

                                    if ($student) {
                                        $student->update([
                                            'deleted_at'      => null,
                                            'group_id'        => $record->group_id,
                                            'email'           => $record->email,
                                            'telefono'        => $record->telefono,
                                            'baja_alumno'     => 0,
                                            'sit_de_estudios' => null,
                                            'sit_academica'   => null,
                                            'etapa_asp'       => 'INSCRITO',
                                        ]);
                                    } else {
                                        $student = Student::create([
                                            'group_id'         => $record->group_id,
                                            'nombre'           => $record->nombre,
                                            'apellido_paterno' => $record->apellido_paterno,
                                            'apellido_materno' => $record->apellido_materno,
                                            'curp'             => $record->curp,
                                            'email'            => $record->email,
                                            'telefono'         => $record->telefono,
                                            'fecha_nacimiento' => $record->fecha_nacimiento ?? '2000-01-01',
                                            'baja_alumno'      => 0,
                                            'etapa_asp'        => 'INSCRITO',
                                            'matricula'        => $record->matricula,
                                        ]);
                                    }

                                    $record->pagos()->update([
                                        'student_id' => $student->id,
                                        'graduated_student_id' => null
                                    ]);

                                    $record->forceDelete();
                                });

                                Notification::make()
                                    ->title('Alumno Reactivado')
                                    ->success()
                                    ->send();

                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Error al reactivar')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGraduatedStudents::route('/'),
            'edit' => Pages\EditGraduatedStudent::route('/{record}/edit'),
            'view' => Pages\ViewGraduatedStudent::route('/{record}'),
        ];
    }
}