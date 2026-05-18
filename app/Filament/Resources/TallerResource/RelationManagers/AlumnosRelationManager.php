<?php
namespace App\Filament\Resources\TallerResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Resources\TallerResource\RelationManagers\AlumnosRelationManager;


class AlumnosRelationManager extends RelationManager
{
    protected static string $relationship = 'alumnos';

    protected static ?string $title = 'Alumnos del Taller';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('debe_material')
                    ->label('Debe Material'),

                Forms\Components\Textarea::make('observaciones')
                    ->label('Observaciones'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Alumno')
                    ->searchable(),

                IconColumn::make('pivot.debe_material')
                    ->label('Debe Material')
                    ->boolean(),

                TextColumn::make('pivot.observaciones')
                    ->label('Observaciones'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
            ]);
    }

public static function getRelations(): array
{
    return [
        AlumnosRelationManager::class,
    ];
}

}
