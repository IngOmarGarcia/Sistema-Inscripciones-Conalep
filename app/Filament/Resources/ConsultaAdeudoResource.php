<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsultaAdeudoResource\Pages;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class ConsultaAdeudoResource extends Resource
{
    protected static ?string $model = Student::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench';
    protected static ?string $navigationLabel = 'Ver Adeudos';
    protected static ?string $navigationGroup = 'Académico';
    protected static ?string $pluralLabel = 'Adeudos en los Talleres'; 
    protected static ?int $navigationSort = 2; 

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Información del Alumno')
                    ->icon('heroicon-m-user-circle')
                    ->schema([
                        Components\TextEntry::make('matricula')->label('Matrícula')->weight('bold'),
                        Components\TextEntry::make('nombre_completo')
                            ->label('Nombre Completo')
                            ->state(fn ($record) => "{$record->nombre} {$record->apellido_paterno} {$record->apellido_materno}"),
                        Components\TextEntry::make('group.name')
                            ->label('Grupo')
                            ->badge()
                            ->color('info')
                            ->default('Sin grupo asignado'),
                    ])->columns(3),

                Components\Section::make('Historial de Talleres y Adeudos')
                    ->icon('heroicon-m-clipboard-document-list')
                    ->schema([
                        Components\RepeatableEntry::make('talleres_detallados')
                            ->label('')
                            ->state(function ($record) {
                                if (!$record->group_id) return [];

                                return DB::table('group_taller')
                                    ->where('group_id', $record->group_id)
                                    ->join('talleres', 'group_taller.taller_id', '=', 'talleres.id')
                                    ->leftJoin('users', 'talleres.user_id', '=', 'users.id')
                                    ->leftJoin('alumno_taller', function($join) use ($record) {
                                        $join->on('talleres.id', '=', 'alumno_taller.taller_id')
                                             ->where('alumno_taller.alumno_id', '=', $record->id);
                                    })
                                    ->select([
                                        'talleres.nombre as taller_nombre',
                                        'users.name as maestro_nombre',
                                        'alumno_taller.debe_material',
                                        'alumno_taller.observaciones',
                                    ])
                                    ->get()
                                    ->map(function ($item) {
                                        return [
                                            'taller_nombre' => $item->taller_nombre,
                                            'maestro_nombre' => $item->maestro_nombre ?? 'No asignado',
                                            'debe_material' => (bool) ($item->debe_material ?? false),
                                            'observaciones' => $item->observaciones ?? 'Sin observaciones.',
                                        ];
                                    })
                                    ->toArray();
                            })
                            ->schema([
                                Components\Grid::make(3)
                                    ->schema([
                                        Components\TextEntry::make('taller_nombre')->label('Taller')->weight('bold')->color('primary'),
                                        Components\TextEntry::make('maestro_nombre')->label('Instructor')->icon('heroicon-m-user'),
                                        Components\IconEntry::make('debe_material')->label('Estado')->boolean(),
                                    ]),
                                Components\TextEntry::make('observaciones')
                                    ->label('Notas')
                                    ->color('gray'),
                            ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();

                // 1. Cargamos relaciones y el conteo de adeudos
                $query->with(['group'])
                    ->select('students.*') 
                    ->addSelect([
                        'total_adeudos' => DB::table('alumno_taller')
                            ->whereColumn('alumno_id', 'students.id')
                            ->where('debe_material', true)
                            ->selectRaw('count(*)')
                    ]);

                // 2. FILTRO DE SEGURIDAD PARA ENCARGADO
                // Si es Encargado, solo ve alumnos que estén en grupos asignados a SUS talleres
                if ($user->hasRole('Encargado')) {
                    $query->whereHas('group.talleres', function ($q) use ($user) {
                        $q->where('talleres.user_id', $user->id);
                    });
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('matricula')->label('Matrícula')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Alumno')
                    ->state(fn($record) => "{$record->nombre} {$record->apellido_paterno} {$record->apellido_materno}")
                    ->searchable(['nombre', 'apellido_paterno', 'apellido_materno']),
                Tables\Columns\TextColumn::make('group.name')->label('Grupo')->sortable()->badge()->color('gray'),
                Tables\Columns\IconColumn::make('total_adeudos')
                    ->label('Adeudo')
                    ->boolean()
                    ->state(fn($record) => $record->total_adeudos > 0)
                    ->trueIcon('heroicon-s-exclamation-triangle')->trueColor('danger')
                    ->falseIcon('heroicon-s-check-circle')->falseColor('success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group_id')
                    ->label('Grupo')
                    ->relationship('group', 'name'),
                
                Tables\Filters\TernaryFilter::make('deudores')
                    ->label('Filtrar Adeudos')
                    ->placeholder('Todos')
                    ->trueLabel('Con adeudos')
                    ->falseLabel('Sin adeudos')
                    ->queries(
                        true: fn (Builder $query) => $query->whereExists(fn ($q) => 
                            $q->select(DB::raw(1))->from('alumno_taller')
                                ->whereColumn('alumno_id', 'students.id')
                                ->where('debe_material', true)
                        ),
                        false: fn (Builder $query) => $query->whereNotExists(fn ($q) => 
                            $q->select(DB::raw(1))->from('alumno_taller')
                                ->whereColumn('alumno_id', 'students.id')
                                ->where('debe_material', true)
                        ),
                    )
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Ver'),
                Tables\Actions\Action::make('gestionar_adeudos')
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'Encargado']))
                    ->form([
                        Forms\Components\Select::make('taller_id')
                            ->label('Taller')
                            ->options(fn($record) => 
                                DB::table('talleres')
                                    ->join('group_taller', 'talleres.id', '=', 'group_taller.taller_id')
                                    ->where('group_taller.group_id', $record->group_id)
                                    // Filtramos también las opciones del Select para que el encargado 
                                    // solo pueda editar adeudos de SUS propios talleres
                                    ->when(auth()->user()->hasRole('Encargado'), fn($q) => $q->where('talleres.user_id', auth()->id()))
                                    ->pluck('talleres.nombre', 'talleres.id')
                            )
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function($state, $set, $record) {
                                $adeudo = DB::table('alumno_taller')->where('alumno_id', $record->id)->where('taller_id', $state)->first();
                                $set('debe_material', $adeudo ? (bool)$adeudo->debe_material : false);
                                $set('observaciones', $adeudo ? $adeudo->observaciones : '');
                            }),
                        Forms\Components\Toggle::make('debe_material')->label('¿Debe material?'),
                        Forms\Components\Textarea::make('observaciones')->label('Notas'),
                    ])
                    ->action(function (array $data, $record) {
                        DB::table('alumno_taller')->updateOrInsert(
                            ['alumno_id' => $record->id, 'taller_id' => $data['taller_id']],
                            ['debe_material' => $data['debe_material'], 'observaciones' => $data['observaciones'], 'updated_at' => now()]
                        );
                    }),
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'escolares', 'Encargado']);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'escolares', 'Encargado']);
    }

    public static function canView($record): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'escolares', 'Encargado']);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListConsultaAdeudos::route('/')];
    }
}