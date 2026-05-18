<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AlumnosPorCarreraChart extends ChartWidget
{
    protected static ?string $heading = 'Total de Alumnos por Carrera';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        /**
         * Según tu base de datos:
         * Tabla: careers -> Columna: name
         * Tabla: groups  -> Columna: career_id
         * Tabla: students -> Columna: group_id
         */
        $data = DB::table('students')
            ->join('groups', 'students.group_id', '=', 'groups.id')
            ->join('careers', 'groups.career_id', '=', 'careers.id')
            ->select('careers.name as carrera_nombre', DB::raw('count(students.id) as total'))
            ->groupBy('careers.name')
            ->pluck('total', 'carrera_nombre');

        return [
            'datasets' => [
                [
                    'label' => 'Total de Alumnos',
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => [
                        '#059669', // Verde Conalep
                        '#3b82f6', // Azul
                        '#f59e0b', // Ambar
                        '#8b5cf6', // Violeta
                    ],
                ],
            ],
            'labels' => $data->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}