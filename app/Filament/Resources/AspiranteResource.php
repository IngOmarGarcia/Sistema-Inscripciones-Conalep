<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AspiranteResource\Pages;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AspiranteResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Aspirantes';
    protected static ?string $navigationGroup = 'Control Escolar';
    protected static ?string $pluralLabel = 'Aspirantes';
    protected static ?string $modelLabel = 'Estudiante';
    protected static ?string $pluralModelLabel = 'Estudiantes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Registro de Aspirante')
                    ->tabs([
                        // --- DATOS PERSONALES ---
                        Forms\Components\Tabs\Tab::make('Personales')
                            ->schema([
                                Forms\Components\TextInput::make('folio_aspirante')
                                    ->label('Folio Aspirante')
                                    ->required(),
                                Forms\Components\TextInput::make('nombre')
                                    ->required(),
                                Forms\Components\TextInput::make('apellido_paterno')
                                    ->label('Primer Apellido')
                                    ->required(),
                                Forms\Components\TextInput::make('apellido_materno')
                                    ->label('Segundo Apellido'),
                                Forms\Components\TextInput::make('curp')
                                    ->label('CURP')
                                    ->required()
                                    ->length(18) 
                                    ->unique(ignoreRecord: true),
                                Forms\Components\Select::make('sexo')
                                    ->options(['M' => 'Masculino', 'F' => 'Femenino']),
                                
                                // CAMBIO AQUÍ: Añadimos ->required()
                                Forms\Components\DatePicker::make('fecha_nacimiento')
                                    ->label('Fecha Nac.')
                                    ->native(false)
                                    ->required(), // Esta línea activa la validación y la etiqueta obligatoria

                                Forms\Components\TextInput::make('telefono')
                                    ->tel()
                                    ->label('Teléfono'),
                                Forms\Components\TextInput::make('tel_celular')
                                    ->tel()
                                    ->label('Celular'),
                            ])->columns(3),

                        // --- ACADÉMICO ---
                        Forms\Components\Tabs\Tab::make('Académico')
                            ->schema([
                                Forms\Components\TextInput::make('matricula')
                                    ->label('Matrícula (Opcional al inicio)'),
                                Forms\Components\TextInput::make('etapa_asp')
                                    ->label('Estatus Etapa')
                                    ->default('ASPIRANTE')
                                    ->readOnly(),
                                Forms\Components\TextInput::make('plan_estudios')
                                    ->label('Plan Estudio'),
                                Forms\Components\TextInput::make('plan_estudios_cve')
                                    ->label('Cv Plan Estudio'),
                                Forms\Components\TextInput::make('modelo_educativo')
                                    ->label('Modelo Educ'),
                                Forms\Components\TextInput::make('periodo_escolar_activo')
                                    ->label('Periodo Registro'),
                                Forms\Components\Toggle::make('encuesta_contestada')
                                    ->label('¿Encuesta contestada?'),
                                Forms\Components\TextInput::make('tot_aciertos_exm')
                                    ->label('Aciertos Examen')
                                    ->numeric(),
                            ])->columns(3),

                        // --- PROCEDENCIA Y DOMICILIO ---
                        Forms\Components\Tabs\Tab::make('Procedencia y Domicilio')
                            ->schema([
                                Forms\Components\TextInput::make('secundaria_nombre')
                                    ->label('Nombre Secundaria'),
                                Forms\Components\TextInput::make('secundaria_cct')
                                    ->label('CCT Secundaria'),
                                Forms\Components\TextInput::make('secundaria_prom')
                                    ->label('Promedio Secundaria')
                                    ->numeric(),
                                Forms\Components\TextInput::make('dom_cp')
                                    ->label('Código Postal'),
                                Forms\Components\TextInput::make('dom_colonia')
                                    ->label('Colonia'),
                                Forms\Components\TextInput::make('dom_ent_fed')
                                    ->label('Entidad Federativa'),
                                Forms\Components\TextInput::make('municipio')
                                    ->label('Municipio'),
                            ])->columns(2),
                    ])->columnSpanFull()
            ]);
    }

    // ... (El resto del código de la tabla y métodos permanece igual)
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('folio_aspirante')
                    ->label('Folio')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->description(fn (Student $record): string => "{$record->apellido_paterno} {$record->apellido_materno}")
                    ->searchable(),
                Tables\Columns\TextColumn::make('curp')
                    ->label('CURP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('etapa_asp')
                    ->label('Etapa')
                    ->badge()
                    ->color('warning'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('aceptar')
                    ->label('Inscribir')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar Inscripción')
                    ->modalDescription('Al aceptar, el aspirante pasará a la lista de Alumnos Inscritos.')
                    ->action(function (Student $record) {
                        $record->update([
                            'etapa_asp' => 'INSCRITO',
                        ]);
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('etapa_asp', 'ASPIRANTE')
            ->whereNull('deleted_at');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAspirantes::route('/'),
            'create' => Pages\CreateAspirante::route('/create'),
            'edit' => Pages\EditAspirante::route('/{record}/edit'),
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