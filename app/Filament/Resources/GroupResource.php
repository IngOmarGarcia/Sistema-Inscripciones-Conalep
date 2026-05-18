<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupResource\Pages;
use App\Filament\Resources\GroupResource\RelationManagers;
use App\Models\Group;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?string $navigationLabel = 'Grupos';
    protected static ?string $pluralLabel = 'Grupos';

    /* =====================================================
        FILTRAR GRUPOS SEGÚN EL USUARIO
       ===================================================== */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = auth()->user();

        // Tutor: solo ve su grupo asignado
        if ($user->hasRole('Tutoria')) {
            return $query->where('tutor_id', $user->id);
        }

        // Superadmin y otros roles: ven todo
        return $query;
    }

    /* =====================================================
        PERMISOS
       ===================================================== */
    public static function canCreate(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();

        if ($user->hasRole('Tutoria')) {
            return $record->tutor_id === $user->id;
        }

        return true;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasRole( 'superadmin');
    }

    /* =====================================================
        FORMULARIO
       ===================================================== */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles del Grupo')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Grupo')
                            ->required(),

                        Forms\Components\Select::make('career_id')
                            ->label('Carrera / Especialidad')
                            ->relationship('career', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Hidden::make('turno')
                            ->default('Matutino'),

                        /* 👨‍🏫 ASIGNAR TUTOR (SOLO SUPERADMIN) */
                        Forms\Components\Select::make('tutor_id')
                            ->label('Tutor del grupo')
                            ->options(
                                User::role('Tutoria')->pluck('name', 'id')
                            )
                            ->searchable()
                            ->required()
                            ->visible(fn () => auth()->user()->hasRole('super_admin')),
                    ])
                    ->columns(2),
            ]);
    }

    /* =====================================================
        TABLA
       ===================================================== */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Grupo')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('career.name')
                    ->label('Carrera')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('students_count')
                    ->label('Total Alumnos')
                    ->counts('students'),
            ])
           ->actions([
    Tables\Actions\ViewAction::make(),

    Tables\Actions\Action::make('asistencia')
        ->label('Asistencia')
        ->icon('heroicon-o-clipboard-document-check')
        ->url(fn ($record) => static::getUrl('asistencia-grupal', [
            'record' => $record,
        ]))
        ->color('success'),

    Tables\Actions\EditAction::make()
        ->visible(fn ($record) =>
            auth()->user()->hasRole('superadmin')
        ),

    Tables\Actions\DeleteAction::make()
        ->visible(fn () => auth()->user()->hasRole('superadmin')),
]);
    }

    /* =====================================================
        RELACIONES
       ===================================================== */
    public static function getRelations(): array
    {
        return [
            RelationManagers\ReunionesRelationManager::class,
            RelationManagers\StudentsRelationManager::class,
        ];
    }

    /* =====================================================
        PÁGINAS
       ===================================================== */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
            'view' => Pages\ViewGroup::route('/{record}'),
            'asistencia-grupal' => Pages\AsistenciaGrupal::route('/{record}/asistencia'),
        ];
    }


 //--------------PERMISOS DE VISUALIZACION--------------
        public static function shouldRegisterNavigation(): bool
{
    return auth()->user()->hasAnyRole([
        
        'super_admin',
        'Tutoria',

    ]);
}

public static function canViewAny(): bool
{
    return auth()->user()->hasAnyRole([
        'super_admin',
        'Tutoria',
    ]);
}


}
