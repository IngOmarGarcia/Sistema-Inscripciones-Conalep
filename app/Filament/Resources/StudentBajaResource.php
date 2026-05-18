<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentBajaResource\Pages;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class StudentBajaResource extends Resource
{
    protected static ?string $model = Student::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box-x-mark';
    protected static ?string $navigationLabel = 'Bajas de Alumnos';
    protected static ?string $navigationGroup = 'Control Escolar';
    protected static ?int $navigationSort = 10;
    protected static ?string $modelLabel = 'Baja';
    protected static ?string $pluralModelLabel = 'Bajas';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Expediente de Baja')
                    ->tabs([
                        // PESTAÑA ACADÉMICA (SOLO LECTURA)
                        Forms\Components\Tabs\Tab::make('Académico')
                            ->schema([
                                Forms\Components\TextInput::make('matricula')->label('Matrícula')->disabled(),
                                Forms\Components\Select::make('group_id')->label('Grupo')->relationship('group', 'name')->disabled(),
                                Forms\Components\TextInput::make('monto_pagado')->label('Monto Pagado Inscripción ($)')->prefix('$')->disabled(),
                                
                                // CORRECCIÓN AQUÍ: Agregamos formato y zona horaria
                                Forms\Components\DateTimePicker::make('deleted_at')
                                    ->label('Fecha y Hora de la Baja')
                                    ->displayFormat('d/m/Y H:i')
                                    ->timezone('America/Mexico_City') 
                                    ->disabled(),

                                Forms\Components\TextInput::make('sit_de_estudios')->label('Situación Estudios')->disabled(),
                                Forms\Components\TextInput::make('periodo_escolar_activo')->disabled(),
                            ])->columns(3),

                        // PESTAÑA PERSONALES
                        Forms\Components\Tabs\Tab::make('Personales')
                            ->schema([
                                Forms\Components\TextInput::make('nombre')->disabled(),
                                Forms\Components\TextInput::make('apellido_paterno')->disabled(),
                                Forms\Components\TextInput::make('apellido_materno')->disabled(),
                                Forms\Components\TextInput::make('curp')->label('CURP')->disabled(),
                                Forms\Components\TextInput::make('email')->label('Email Institucional')->disabled(),
                                Forms\Components\TextInput::make('telefono')->tel()->disabled(),
                                Forms\Components\TextInput::make('tel_celular')->tel()->disabled(),
                            ])->columns(3),

                        // PESTAÑA MÉDICOS
                        Forms\Components\Tabs\Tab::make('Médicos')
                            ->schema([
                                Forms\Components\TextInput::make('medico_institucion_medica')->disabled(),
                                Forms\Components\TextInput::make('medico_alergia')->disabled(),
                                Forms\Components\TextInput::make('medico_cardiopatia')->disabled(),
                                Forms\Components\TextInput::make('medico_epilepsia')->disabled(),
                            ])->columns(2),

                        // PESTAÑA HISTORIAL DE PAGOS
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
                                            Forms\Components\DateTimePicker::make('created_at')
                                                ->label('Fecha Emisión')
                                                ->displayFormat('d/m/Y H:i')
                                                ->readOnly(),
                                            Forms\Components\ViewField::make('pdf_path')
                                                ->label('Descargar')
                                                ->view('filament.forms.components.download-pdf-link'),
                                        ]),
                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TagsInput::make('conceptos')
                                                ->label('Conceptos Incluidos')
                                                ->separator(',')
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
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('matricula')
                    ->label('Matrícula')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Alumno')
                    ->description(fn (Student $record): string => "{$record->apellido_paterno} {$record->apellido_materno}")
                    ->searchable(['nombre', 'apellido_paterno', 'apellido_materno']),
                Tables\Columns\TextColumn::make('group.name')
                    ->label('Grupo anterior')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Fecha de Baja')
                    ->dateTime('d/m/Y H:i') // Formato consistente
                    ->timezone('America/Mexico_City') // Asegura que la tabla muestre la hora local
                    ->sortable()
                    ->color('danger'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\RestoreAction::make()
                    ->label('Reingresar')
                    ->color('success'),
                Tables\Actions\ForceDeleteAction::make()
                    ->label('Borrar Permanente'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->onlyTrashed()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentBajas::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'escolares']);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'escolares', 'Tutoria']);
    }
}