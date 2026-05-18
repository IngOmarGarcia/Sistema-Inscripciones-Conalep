<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CareerResource\Pages;
use App\Models\Career;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CareerResource extends Resource
{
    protected static ?string $model = Career::class;

    // Cambiamos el icono a uno académico
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    // Nombre en el menú lateral
    protected static ?string $navigationLabel = 'Carreras';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Carrera')
                    ->description('Administra las especialidades técnicas del plantel.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de la Carrera')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej. Informática'),

                        Forms\Components\TextInput::make('clave')
                            ->label('Clave de la Carrera')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('Ej. INFO-04'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('clave')
                    ->label('Clave')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Carrera')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Borrar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCareers::route('/'),
            'create' => Pages\CreateCareer::route('/create'),
           'view' => Pages\ViewCareer::route('/{record}'),
            'edit' => Pages\EditCareer::route('/{record}/edit'),
        ];
    }

    //--------------PERMISOS DE VISUALIZACION--------------
        public static function shouldRegisterNavigation(): bool
{
    return auth()->user()->hasAnyRole([
        'super_admin',
        
    ]);
}

public static function canViewAny(): bool
{
    return auth()->user()->hasAnyRole([
        'super_admin',

        
    ]);
}

}