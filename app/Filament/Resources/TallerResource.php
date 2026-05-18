<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TallerResource\Pages;
use App\Filament\Resources\TallerResource\RelationManagers;
use App\Models\Taller;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteBulkAction;

class TallerResource extends Resource
{
    protected static ?string $model = Taller::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $pluralLabel = 'Talleres';

    protected static ?string $navigationLabel = 'Talleres';

    protected static ?string $navigationGroup = 'Académico';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->label('Nombre del Taller')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn () => !auth()->user()->hasAnyRole(['super_admin', 'Encargado'])),

                Select::make('user_id')
                    ->label('Maestro Encargado')
                    ->relationship('encargado', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled(fn () => !auth()->user()->hasAnyRole(['super_admin', 'Encargado'])),
                    
                Select::make('groups')
                    ->label('Grupos Asignados')
                    ->relationship('groups', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->disabled(fn () => !auth()->user()->hasAnyRole(['super_admin', 'Encargado'])),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre del Taller')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('encargado.name')
                    ->label('Maestro Encargado')
                    ->sortable()
                    ->default('Sin asignar'),

                TextColumn::make('groups.name')
                    ->label('Grupos')
                    ->badge()
                    ->separator(','),
            ])
            ->filters([])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn ($record) => 
                        auth()->user()->hasAnyRole(['super_admin', 'Encargado']) || 
                        (auth()->user()->hasRole('Taller') && $record->user_id === auth()->user()->id)
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'Encargado'])),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StudentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTallers::route('/'),
            'create' => Pages\CreateTaller::route('/create'),
            'edit' => Pages\EditTaller::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole([
            'super_admin',
            'Taller',
            'escolares',
            'Encargado',
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery();

        // Si es Admin o Escolares, ve TODO
        if ($user->hasAnyRole(['super_admin', 'escolares'])) {
            return $query;
        }

        // Si es Encargado o tiene el rol Taller, SOLO ve lo que tiene asignado
        if ($user->hasAnyRole(['Encargado', 'Taller'])) {
            return $query->where('user_id', $user->id);
        }

        // Por seguridad, si no tiene roles permitidos, no ve nada
        return $query->whereRaw('0 = 1');
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();

        if ($user->hasAnyRole(['super_admin', 'Encargado'])) {
            // Un Encargado solo debería poder editar su propio taller si no es Super Admin
            if ($user->hasRole('Encargado') && !$user->hasRole('super_admin')) {
                return $record->user_id === $user->id;
            }
            return true;
        }

        if ($user->hasRole('Taller')) {
            return $record->user_id === $user->id;
        }

        return false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'Encargado']);
    }

    public static function canDelete($record): bool
    {
        // Solo el super admin debería poder borrar talleres físicamente
        // O si quieres que el encargado pueda, déjalo así:
        return auth()->user()->hasAnyRole(['super_admin', 'Encargado']);
    }
}