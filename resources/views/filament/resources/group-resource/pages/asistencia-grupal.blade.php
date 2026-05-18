<x-filament-panels::page>
@php
    $alumnos = $group->students ?? collect();
    $reuniones = $group->reuniones ?? collect();

    $totalAlumnos = $alumnos->count();
    $totalReuniones = $reuniones->count();

    $totalPosibles = $totalAlumnos * $totalReuniones;

    $totalAsistencias = $group->asistencias
        ->where('asistio', true)
        ->count();

    $porcentaje = $totalPosibles > 0
        ? round(($totalAsistencias / $totalPosibles) * 100)
        : 0;

    if ($porcentaje === 100) {
        $nivel = 'Alto';
        $color = 'success';
    } elseif ($porcentaje >= 67) {
        $nivel = 'Medio';
        $color = 'warning';
    } else {
        $nivel = 'Bajo';
        $color = 'danger';
    }
@endphp

<div class="space-y-6">

    {{-- 🔹 RESUMEN --}}
    <x-filament::section>
        <x-slot name="heading">
            Asistencia grupal
        </x-slot>

        {{-- 👇 CONTENEDOR PROPIO (IMPORTANTE) --}}
        <div class="w-full">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Alumnos --}}
                <div class="p-4 rounded bg-white dark:bg-gray-800">
                    <p class="text-sm text-gray-500">Alumnos</p>
                    <p class="text-2xl font-bold">
                        {{ $totalAlumnos }}
                    </p>
                </div>

                {{-- Reuniones --}}
                <div class="p-4 rounded bg-white dark:bg-gray-800">
                    <p class="text-sm text-gray-500">Reuniones</p>
                    <p class="text-2xl font-bold">
                        {{ $totalReuniones }}
                    </p>
                </div>

                {{-- Participación --}}
                <div class="p-4 rounded bg-white dark:bg-gray-800">
                    <p class="text-sm text-gray-500">Participación</p>

                    <p class="text-2xl font-bold"
                       style="
                            color:
                            @if ($color === 'success')
                                rgb(var(--success-600))
                            @elseif ($color === 'warning')
                                rgb(var(--warning-600))
                            @else
                                rgb(var(--danger-600))
                            @endif
                       ">
                        {{ $porcentaje }}%
                    </p>
                </div>

                {{-- Nivel --}}
                <div class="p-4 rounded bg-white dark:bg-gray-800">
                    <p class="text-sm text-gray-500">Nivel de acompañamiento</p>

                    <p class="text-2xl font-bold"
                       style="
                            color:
                            @if ($color === 'success')
                                rgb(var(--success-600))
                            @elseif ($color === 'warning')
                                rgb(var(--warning-600))
                            @else
                                rgb(var(--danger-600))
                            @endif
                       ">
                        {{ $nivel }}
                    </p>
                </div>

            </div>
        </div>
    </x-filament::section>

    {{-- 🔹 TABLA POR ALUMNO --}}
    <x-filament::section>
        <x-slot name="heading">
            Detalle por alumno
        </x-slot>

        <table class="w-full text-sm border border-gray-200 dark:border-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="p-2 text-left">Alumno</th>
                    <th class="p-2 text-center">Asistencias</th>
                    <th class="p-2 text-center">%</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($alumnos as $alumno)
                    @php
                        $asistenciasAlumno = $group->asistencias
                            ->where('student_id', $alumno->id)
                            ->where('asistio', true)
                            ->count();

                        $porcentajeAlumno = $totalReuniones > 0
                            ? round(($asistenciasAlumno / $totalReuniones) * 100)
                            : 0;

                        if ($porcentajeAlumno === 100) {
                            $colorAlumno = 'success';
                        } elseif ($porcentajeAlumno >= 67) {
                            $colorAlumno = 'warning';
                        } else {
                            $colorAlumno = 'danger';
                        }
                    @endphp

                    <tr class="border-t border-gray-200 dark:border-gray-700">
                        <td class="p-2">
                            {{ $alumno->nombre }}
                            {{ $alumno->apellido_paterno }}
                            {{ $alumno->apellido_materno }}
                        </td>

                        <td class="p-2 text-center">
                            {{ $asistenciasAlumno }} / {{ $totalReuniones }}
                        </td>

                        <td class="p-2 text-center font-bold"
                            style="
                                color:
                                @if ($colorAlumno === 'success')
                                    rgb(var(--success-600))
                                @elseif ($colorAlumno === 'warning')
                                    rgb(var(--warning-600))
                                @else
                                    rgb(var(--danger-600))
                                @endif
                            ">
                            {{ $porcentajeAlumno }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3"
                            class="p-4 text-center text-gray-500">
                            No hay alumnos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-filament::section>

</div>
</x-filament-panels::page>
