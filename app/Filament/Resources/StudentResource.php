<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use App\Models\Group;
use App\Models\GraduatedStudent;
use App\Models\Pago;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action; 
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Barryvdh\DomPDF\Facade\Pdf; 
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder; 
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Alumnos Activos';
    protected static ?string $navigationGroup = 'Control Escolar';
    protected static ?string $modelLabel = 'Alumno';
    protected static ?string $pluralModelLabel = 'Alumnos';

    const COSTO_INSCRIPCION = 1100;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['group', 'pagos']) 
            ->where('etapa_asp', 'INSCRITO'); 
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Expediente')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Académico')
                            ->schema([
                                Forms\Components\TextInput::make('matricula')->label('Matrícula')->required()->unique(ignoreRecord: true),
                                Forms\Components\Select::make('group_id')->label('Grupo')->relationship('group', 'name')->searchable()->preload(),
                                
                                Forms\Components\Select::make('estatus_inscripcion')
                                    ->label('Estatus de Inscripción')
                                    ->options([
                                        'Pendiente' => 'Pendiente',
                                        'En proceso' => 'En proceso',
                                        'Parcial' => 'Parcial',
                                        'Completo' => 'Completo',
                                    ])
                                    ->default('Pendiente'),
                                
                                Forms\Components\Select::make('talleres')
                                    ->relationship('talleres', 'nombre')
                                    ->multiple()
                                    ->preload()
                                    ->label('Asignar a Talleres'),

                                Forms\Components\TextInput::make('monto_pagado')->label('Monto Pagado Inscripción ($)')->numeric()->prefix('$')->default(0),
                                Forms\Components\TextInput::make('prom_gral')->numeric(),
                                Forms\Components\TextInput::make('modelo_educativo'),
                                Forms\Components\TextInput::make('plan_estudios_cve'),
                                Forms\Components\TextInput::make('plan_estudios'),
                                Forms\Components\TextInput::make('periodo_escolar_activo'),
                                Forms\Components\TextInput::make('sit_de_estudios'),
                                Forms\Components\TextInput::make('sit_academica'),
                                Forms\Components\TextInput::make('prom_pdo_escolar_anterior')->numeric(),
                                Forms\Components\TextInput::make('secundaria_cct'),
                                Forms\Components\TextInput::make('secundaria_prom')->numeric(),
                                Forms\Components\TextInput::make('folio_p_prof'),
                                Forms\Components\TextInput::make('folio_serv_soc'),
                                Forms\Components\Toggle::make('baja_alumno')->onColor('danger'),
                            ])->columns(3),

                        Forms\Components\Tabs\Tab::make('Personales')
                            ->schema([
                                Forms\Components\TextInput::make('nombre')->required(),
                                Forms\Components\TextInput::make('apellido_paterno')->required(),
                                Forms\Components\TextInput::make('apellido_materno'),
                                Forms\Components\TextInput::make('curp')->label('CURP')->required()->length(18)->unique(ignoreRecord: true),
                                Forms\Components\Select::make('situacion_curp')->options(['Validada' => 'Validada', 'Pendiente' => 'Pendiente']),
                                Forms\Components\Select::make('sexo')->options(['M' => 'Masculino', 'F' => 'Femenino']),
                                Forms\Components\TextInput::make('edad')->numeric(),
                                Forms\Components\DatePicker::make('fecha_nacimiento')->native(false),
                                Forms\Components\DatePicker::make('nac_fecha_reg')->native(false),
                                Forms\Components\TextInput::make('email')->label('Email Institucional')->email(),
                                Forms\Components\TextInput::make('telefono')->tel(),
                                Forms\Components\TextInput::make('tel_celular')->tel(),
                                Forms\Components\TextInput::make('folio_aspirante'),
                                Forms\Components\TextInput::make('etapa_asp')->default('INSCRITO'),
                                Forms\Components\TextInput::make('situacion_del_dato'),
                                Forms\Components\TextInput::make('doc_probatorio'),
                            ])->columns(3),

                        Forms\Components\Tabs\Tab::make('Domicilio')
                            ->schema([
                                Forms\Components\TextInput::make('calle'),
                                Forms\Components\TextInput::make('dom_colonia'),
                                Forms\Components\TextInput::make('dom_cp'),
                                Forms\Components\TextInput::make('dom_ciudad'),
                                Forms\Components\TextInput::make('municipio'),
                                Forms\Components\TextInput::make('dom_ent_fed'),
                                Forms\Components\TextInput::make('dom_cv_asentamiento'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Médicos')
                            ->schema([
                                Forms\Components\TextInput::make('medico_institucion_medica'),
                                Forms\Components\TextInput::make('medico_unidad_de_salud'),
                                Forms\Components\TextInput::make('medico_no_u_de_salud'),
                                Forms\Components\TextInput::make('medico_cv_filiacion'),
                                Forms\Components\TextInput::make('medico_alergia'),
                                Forms\Components\TextInput::make('medico_cardiopatia'),
                                Forms\Components\TextInput::make('medico_epilepsia'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Tutor')
                            ->schema([
                                Forms\Components\TextInput::make('tutor_nombre'),
                                Forms\Components\TextInput::make('tutor_correo')->email(),
                                Forms\Components\TextInput::make('tutor_telefono')->tel(),
                                Forms\Components\TextInput::make('tutor_celular')->tel(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Historial de Pagos')
                            ->icon('heroicon-o-receipt-percent')
                            ->schema([
                                Forms\Components\Repeater::make('pagos')
                                    ->relationship('pagos')
                                    ->schema([
                                        Forms\Components\Grid::make(5)->schema([
                                            Forms\Components\TextInput::make('folio')->label('Folio')->readOnly(),
                                            Forms\Components\TextInput::make('periodo')->label('Periodo')->readOnly(),
                                            Forms\Components\TextInput::make('monto_total')->label('Total Recibo')->prefix('$')->readOnly(),
                                            Forms\Components\DateTimePicker::make('created_at')->label('Fecha Emisión')->readOnly(),
                                            
                                            // --- CORRECCIÓN AQUÍ: BOTÓN DE DESCARGA ---
                                            Forms\Components\Placeholder::make('pdf_path_link')
                                                ->label('Acción')
                                                ->content(function ($record) {
                                                    if (!$record || !$record->pdf_path) return 'No disponible';
                                                    $url = route('ver.pdf', ['path' => $record->pdf_path]);
                                                    return new HtmlString("
                                                        <a href='{$url}' target='_blank' 
                                                           style='background-color: #059669; color: white; padding: 8px 16px; border-radius: 6px; font-weight: bold; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;'>
                                                            <svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z'></path></svg>
                                                            Ver PDF
                                                        </a>
                                                    ");
                                                }),
                                        ]),
                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TagsInput::make('conceptos')->label('Conceptos Incluidos')->separator(',')->disabled(), 
                                            Forms\Components\TextInput::make('abono_inscripcion')->label('Abonado a Inscripción')->prefix('$')->readOnly()->extraInputAttributes(['class' => 'text-success-600 font-bold']),
                                        ]),
                                    ])
                                    ->itemLabel(fn (array $state): ?string => "Recibo Folio: " . ($state['folio'] ?? 'S/N'))
                                    ->collapsible()
                                    ->collapsed()
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                            ]),
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('matricula')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->description(fn (Student $record): string => "{$record->apellido_paterno} {$record->apellido_materno}")
                    ->searchable(['nombre', 'apellido_paterno', 'apellido_materno']), 
                Tables\Columns\TextColumn::make('group.name')->badge()->color('info')->label('Grupo')->searchable(),
                Tables\Columns\TextColumn::make('estatus_inscripcion')
                    ->label('Estatus Inscripción/Pago')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Completo' => 'success',
                        'En proceso' => 'warning',
                        'Parcial' => 'info',
                        'Pendiente' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (?string $state): string => match ($state) {
                        'Completo' => 'heroicon-o-check-circle',
                        'En proceso' => 'heroicon-o-clock',
                        'Parcial' => 'heroicon-o-document-minus',
                        'Pendiente' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-information-circle',
                    }),
            ])
            ->filters([
                SelectFilter::make('group_id')->label('Filtrar por Grupo')->relationship('group', 'name'),
                SelectFilter::make('estatus_inscripcion')
                    ->label('Estatus de Inscripción')
                    ->options([
                        'Completo' => 'Completo',
                        'Parcial' => 'Parcial',
                        'En proceso' => 'En proceso',
                        'Pendiente' => 'Pendiente',
                    ]),
            ])
            ->actions([
                Action::make('generar_ficha')
                    ->label('Inscripción / Pago')
                    ->icon('heroicon-o-document-currency-dollar')
                    ->color('info')
                    ->modalHeading('Generar Ficha de Cobro')
                    ->modalWidth('4xl')
                    ->modalSubmitActionLabel('Descargar PDF')
                    ->form(function() {
                        $conceptosBase = [
                            ['nombre' => 'Aportación por servicio educativo (Inscripción)', 'precio' => 1100],
                            ['nombre' => 'Asesoría complementaria semestral', 'precio' => 150],
                            ['nombre' => 'Asesoría complementaria intersemestral', 'precio' => 67],
                            ['nombre' => 'Credencial escolar (Anual)', 'precio' => 50],
                            ['nombre' => '1 Módulo', 'precio' => 215],
                            ['nombre' => '2 Módulos', 'precio' => 270],
                            ['nombre' => '3 Módulos', 'precio' => 405],
                            ['nombre' => 'Seguro Estudiantil (Anual)', 'precio' => 100],
                            ['nombre' => 'Credencial Escolar', 'precio' => 50],
                            ['nombre' => 'Gastos de Certificación', 'precio' => 135],
                            ['nombre' => 'Protocolo de Titulación', 'precio' => 500],
                            ['nombre' => 'Derecho a Ev. Diagnóstica', 'precio' => 400],
                            ['nombre' => 'Constancia de Estudio', 'precio' => 50],
                        ];

                        $conceptosMaestros = [];
                        foreach ($conceptosBase as $index => $concepto) {
                            $num = $index + 1;
                            $conceptosMaestros[] = [
                                'id'     => 'c' . $num,
                                'nombre' => $num . '. ' . $concepto['nombre'],
                                'precio' => $concepto['precio']
                            ];
                        }

                        $camposDeTabla = [];
                        $camposDeTabla[] = Forms\Components\Grid::make(12)->schema([
                            Forms\Components\Placeholder::make('h_sel')->content('Sel.')->columnSpan(1),
                            Forms\Components\Placeholder::make('h_con')->content('Concepto')->columnSpan(8),
                            Forms\Components\Placeholder::make('h_mon')->content('Monto ($)')->columnSpan(3),
                        ]);

                        foreach ($conceptosMaestros as $c) {
                            $id = $c['id'];
                            $precio = $c['precio'];
                            $camposDeTabla[] = Forms\Components\Grid::make(12)->schema([
                                Forms\Components\Checkbox::make("check_{$id}")
                                    ->label('')
                                    ->columnSpan(1)
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) use ($id, $precio, $conceptosMaestros) {
                                        $set("monto_{$id}", $state ? $precio : 0);
                                        $nuevoTotal = 0;
                                        foreach ($conceptosMaestros as $item) {
                                            $nuevoTotal += floatval($get("monto_{$item['id']}") ?? 0);
                                        }
                                        $set('total_pagar', $nuevoTotal);
                                    }),
                                Forms\Components\Placeholder::make("lbl_{$id}")->content($c['nombre'])->label('')->columnSpan(8)->extraAttributes(['class' => 'pt-2']),
                                Forms\Components\TextInput::make("monto_{$id}")
                                    ->label('')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0)
                                    ->columnSpan(3)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set) use ($conceptosMaestros) {
                                        $nuevoTotal = 0;
                                        foreach ($conceptosMaestros as $item) {
                                            $nuevoTotal += floatval($get("monto_{$item['id']}") ?? 0);
                                        }
                                        $set('total_pagar', $nuevoTotal);
                                    }),
                            ]);
                        }

                        return [
                            Forms\Components\Section::make('Datos del Alumno')->compact()->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('nombre_completo')->label('Alumno')->default(fn ($record) => "{$record->nombre} {$record->apellido_paterno} {$record->apellido_materno}")->disabled()->dehydrated(false),
                                    Forms\Components\TextInput::make('matricula_display')->label('Matrícula')->default(fn ($record) => $record->matricula)->disabled()->dehydrated(false),
                                ]),
                            ]),
                            Forms\Components\Section::make('Detalles del Trámite')->schema([
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('folio')
                                        ->label('Folio')
                                        ->default(function () {
                                            $maxFolio = \App\Models\Pago::max(\Illuminate\Support\Facades\DB::raw('CAST(folio AS UNSIGNED)'));
                                            return $maxFolio ? (int) $maxFolio + 1 : 1;
                                        })
                                        ->required()
                                        ->readOnly(),
                                    Forms\Components\Select::make('periodo')->options(['2025-2026' => '2025-2026', '2026-2027' => '2026-2027'])->default('2025-2026')->required(),
                                ]),
                                Forms\Components\Radio::make('tipo_tramite')->label('Tipo')->options(['Regular' => 'Regular', 'Readmisión' => 'Readmisión', 'Portabilidad' => 'Portabilidad', 'Transferencia' => 'Transferencia'])->inline()->default('Regular')->required(),
                            ]),
                            Forms\Components\Section::make('Conceptos de Pago')->schema([
                                Forms\Components\Group::make($camposDeTabla), 
                                Forms\Components\Placeholder::make('separador_total')->label('')->content(new HtmlString('<div style="border-top: 1px solid #ccc; margin-top: 10px; margin-bottom: 10px;"></div>')),
                                Forms\Components\TextInput::make('total_pagar')->label('TOTAL A PAGAR')->prefix('$')->default(0)->readOnly()->extraInputAttributes(['style' => 'font-weight: bold; font-size: 1.2rem; text-align: right;']),
                            ]),
                        ];
                    })
                    ->action(function (array $data, Student $record) {
                        $nombresBase = [
                            'Inscripción/Reinscripción', 'Asesoría semestral', 'Asesoría intersemestral',
                            'Credencial Anual', '1 Módulo', '2 Módulos', '3 Módulos', 'Seguro Estudiantil',
                            'Credencial Escolar', 'Gastos Certificación', 'Titulación', 'Evaluación Diag.', 'Constancia',
                        ];
                        
                        $nombres = [];
                        foreach ($nombresBase as $index => $nombre) {
                            $nombres['c' . ($index + 1)] = $nombre;
                        }

                        $conceptosProcesados = [];
                        $nombresParaBD = [];
                        $montoAbonoInscripcion = 0;

                        foreach ($nombres as $id => $nombre) {
                            $estaMarcado = isset($data["check_{$id}"]) && $data["check_{$id}"];
                            $monto = $estaMarcado ? (float)($data["monto_{$id}"] ?? 0) : 0;
                            
                            $conceptosProcesados[] = ['nombre' => $nombre, 'monto'  => $monto, 'seleccionado' => $estaMarcado];
                            
                            if($estaMarcado) {
                                $nombresParaBD[] = $nombre;
                                if($id === 'c1') $montoAbonoInscripcion = $monto;
                            }
                        }

                        $pdfContent = Pdf::loadView('pdf.ficha_inscripcion', [
                            'data' => $data, 'record' => $record, 'conceptos_procesados' => $conceptosProcesados, 
                        ])->setPaper('letter', 'portrait')->output();

                        $nombreArchivo = "recibos/{$record->matricula}-{$data['folio']}.pdf";
                        Storage::disk('public')->put($nombreArchivo, $pdfContent);

                        $record->pagos()->create([
                            'folio' => $data['folio'],
                            'periodo' => $data['periodo'],
                            'monto_total' => $data['total_pagar'],
                            'abono_inscripcion' => $montoAbonoInscripcion,
                            'conceptos' => $nombresParaBD,
                            'pdf_path' => $nombreArchivo,
                        ]);

                        $updateData = ['estatus_inscripcion' => 'En proceso'];
                        if ($montoAbonoInscripcion > 0) {
                            $updateData['monto_pagado'] = ($record->monto_pagado ?? 0) + $montoAbonoInscripcion;
                        }

                        $record->update($updateData);

                        Notification::make()->title('Recibo Generado')->success()->send();

                        return response()->streamDownload(function () use ($pdfContent) {
                            echo $pdfContent;
                        }, 'Ficha-'.$record->matricula.'.pdf');
                    }),

                Action::make('cambiar_estatus')
                    ->label('Estatus')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->color('warning')
                    ->modalHeading('Actualizar Estatus de Inscripción')
                    ->form([
                        Forms\Components\Select::make('estatus_inscripcion')
                            ->label('Selecciona el nuevo estatus')
                            ->options([
                                'Pendiente' => 'Pendiente',
                                'En proceso' => 'En proceso',
                                'Parcial' => 'Parcial',
                                'Completo' => 'Completo',
                            ])
                            ->default(fn (Student $record): ?string => $record->estatus_inscripcion)
                            ->required(),
                    ])
                    ->action(function (Student $record, array $data) {
                        $record->update([
                            'estatus_inscripcion' => $data['estatus_inscripcion']
                        ]);
                        Notification::make()->title('Estatus actualizado')->success()->send();
                    }),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()->label('Dar de Baja')->icon('heroicon-o-archive-box-x-mark')->modalHeading('¿Dar de baja al alumno?'),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('promover_alumnos')
                        ->label('Promover de Grupo/Semestre')
                        ->icon('heroicon-o-arrow-trending-up')
                        ->color('success')
                        ->form([
                            Forms\Components\Select::make('nuevo_grupo_id')
                                ->label('Seleccionar Nuevo Grupo')
                                ->options(Group::pluck('name', 'id'))
                                ->required()
                                ->searchable(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            DB::transaction(function () use ($records, $data) {
                                $records->each(function (Student $student) use ($data) {
                                    $student->update([
                                        'group_id' => $data['nuevo_grupo_id'],
                                        'monto_pagado' => 0,
                                        'estatus_inscripcion' => 'Pendiente', 
                                    ]);
                                });
                            });
                            Notification::make()->title('Alumnos promovidos')->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('graduacion_masiva')
                        ->label('Graduar Alumnos Seleccionados')
                        ->icon('heroicon-o-academic-cap')
                        ->color('info')
                        ->form([
                            Forms\Components\TextInput::make('periodo_egreso')->required()->placeholder('Ej. 2023-2026'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            DB::transaction(function () use ($records, $data) {
                                $records->each(function (Student $student) use ($data) {
                                    if (empty($student->matricula) || empty($student->group_id)) {
                                        return true;
                                    }

                                    $graduated = GraduatedStudent::create([
                                        'nombre' => $student->nombre,
                                        'apellido_paterno' => $student->apellido_paterno,
                                        'apellido_materno' => $student->apellido_materno,
                                        'matricula' => $student->matricula,
                                        'periodo_egreso' => $data['periodo_egreso'],
                                        'group_id' => $student->group_id,
                                        'monto_pagado' => $student->monto_pagado,
                                        'curp' => $student->curp,
                                        'sexo' => $student->sexo,
                                        'email' => $student->email,
                                        'telefono' => $student->telefono,
                                        'tel_celular' => $student->tel_celular,
                                        'calle' => $student->calle,
                                        'dom_colonia' => $student->dom_colonia,
                                        'dom_cp' => $student->dom_cp,
                                        'dom_ciudad' => $student->dom_ciudad,
                                        'dom_ent_fed' => $student->dom_ent_fed,
                                    ]);

                                    \App\Models\Pago::where('student_id', $student->id)->update([
                                        'student_id' => null,
                                        'graduated_student_id' => $graduated->id
                                    ]);

                                    $student->delete();
                                });
                            });
                            Notification::make()->title('Graduación completada')->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}