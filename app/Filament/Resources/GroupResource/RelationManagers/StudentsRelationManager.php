<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'students';
    protected static ?string $title = 'Alumnos en este Grupo';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Completa del Alumno')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('apellido_paterno')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('apellido_materno')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('curp')
                            ->label('CURP')
                            ->maxLength(18),
                        Forms\Components\Select::make('career_id')
                            ->label('Carrera')
                            ->relationship('career', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email(),
                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono'),
                        Forms\Components\TextInput::make('genero')
                            ->label('Género'),
                        Forms\Components\TextInput::make('fecha_nacimiento')
                            ->label('Fecha de Nacimiento'),
                        Forms\Components\Textarea::make('direccion')
                            ->label('Dirección')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                Tables\Columns\TextColumn::make('nombre')->label('Nombre')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('apellido_paterno')->label('Apellido Paterno')->searchable(),
                Tables\Columns\TextColumn::make('career.name')->label('Carrera')->badge()->color('success'),
                Tables\Columns\TextColumn::make('curp')->label('CURP'),
            ])
            ->headerActions([
                // botones de agregar alumnos, si hubiera (ej. Tables\Actions\AttachAction::make())
            ])
            ->actions([
                // Este botón lo ven todos
                Tables\Actions\ViewAction::make()
                    ->label('Ver Expediente'),
                
                // Estos botones se ocultan si el usuario tiene el rol 'escolares'
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->hidden(fn () => auth()->user()->hasRole('escolares')),
                    
                Tables\Actions\DeleteAction::make()
                    ->label('Quitar del Grupo')
                    ->hidden(fn () => auth()->user()->hasRole('escolares')),
                    
                Tables\Actions\Action::make('verAsistencia')
                    ->label('Acompañamiento')
                    ->icon('heroicon-o-user-group')
                    ->color('info')
                    ->modalHeading('Acompañamiento familiar')
                    ->modalContent(fn ($record) => view('filament.alumnos.asistencia', [
                        'alumno' => $record,
                        'group' => $this->getOwnerRecord(),
                    ]))
                    ->hidden(fn () => auth()->user()->hasRole('escolares')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Se oculta la acción masiva de borrar si es 'escolares'
                    Tables\Actions\DeleteBulkAction::make()
                        ->hidden(fn () => auth()->user()->hasRole('escolares')),
                ]),
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['debe_material'] ?? false) === false) {
            $data['observaciones'] = null;
        }

        return $data;
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = $this->getRelationship()->getQuery();

        $user = auth()->user();

        // Cambiado superadmin por super_admin para coincidir con tus roles
        if ($user->hasRole('Taller') || $user->hasRole('super_admin')) {
            return $query;
        }

        if ($user->hasRole('escolares')) {
            // escolares solo pueden ver los alumnos del taller
            return $query; // no agregues filtros de edición, solo usa la relación
        }

        return $query->whereRaw('0 = 1'); // otros roles no ven nada
    }
}