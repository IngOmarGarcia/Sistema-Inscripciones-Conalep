@php
    $reuniones = $alumno->group->reuniones ?? collect();
    $totalReuniones = $reuniones->count();

    $asistenciasTutor = $alumno->asistencias
        ->where('asistio', true)
        ->count();

    $porcentaje = $totalReuniones > 0
        ? round(($asistenciasTutor / $totalReuniones) * 100)
        : 0;

    if ($porcentaje == 100) {
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

    {{-- ENCABEZADO --}}
    <div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            {{ $alumno->nombre }}
            {{ $alumno->apellido_paterno }}
            {{ $alumno->apellido_materno }}
        </h2>

        <p class="text-sm text-gray-500 dark:text-gray-400">
            Carrera: {{ $alumno->career->name ?? 'Sin asignar' }}
        </p>
    </div>

    {{-- RESUMEN --}}
    <div class="grid grid-cols-3 gap-4">

        <div class="p-4 rounded bg-white dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Reuniones</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $totalReuniones }}
            </p>
        </div>

        <div class="p-4 rounded bg-white dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Asistencias</p>
            <p class="text-2xl font-bold"
               style="color: rgb(var(--success-600));">
                {{ $asistenciasTutor }}
            </p>
        </div>

        <div class="p-4 rounded bg-white dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Participación</p>

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

    </div>

    {{-- NIVEL --}}
    <div>
        <p class="font-semibold text-gray-800 dark:text-gray-200">
            Nivel de acompañamiento:
            <span class="font-bold"
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
            </span>
        </p>
    </div>

    {{-- HISTORIAL --}}
    <div>
        <h3 class="font-semibold mb-2 text-gray-900 dark:text-white">
            Historial de reuniones
        </h3>

        <table class="w-full text-sm border border-gray-200 dark:border-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="p-2 text-left text-gray-700 dark:text-gray-200">
                        Reunión
                    </th>
                    <th class="p-2 text-left text-gray-700 dark:text-gray-200">
                        Fecha
                    </th>
                    <th class="p-2 text-center text-gray-700 dark:text-gray-200">
                        Tutor
                    </th>
                    <th class="p-2 text-center text-gray-700 dark:text-gray-200">
                        Asistió
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse ($reuniones as $reunion)
                    @php
                        $asistencia = $alumno->asistencias
                            ->where('reunion_id', $reunion->id)
                            ->first();
                    @endphp

                    <tr class="border-t border-gray-200 dark:border-gray-700">
                        <td class="p-2 text-gray-800 dark:text-gray-200">
                            {{ $reunion->nombre }}
                        </td>

                        <td class="p-2 text-gray-600 dark:text-gray-400">
                            {{ $reunion->fecha
                                ? $reunion->fecha->format('d/m/Y H:i')
                                : 'Sin fecha' }}
                        </td>

                        <td class="p-2 text-center text-gray-700 dark:text-gray-300">
                            {{ $asistencia->tutor_nombre ?? '—' }}
                        </td>

                        <td class="p-2 text-center">
                            @if ($asistencia && $asistencia->asistio)
                                <span style="color: rgb(var(--success-600)); font-weight: bold;">✔</span>
                            @else
                                <span style="color: rgb(var(--danger-600)); font-weight: bold;">✘</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4"
                            class="p-4 text-center text-gray-500 dark:text-gray-400">
                            No hay reuniones registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
