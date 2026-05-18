<x-filament::section>
    <x-slot name="heading">
        Pasar lista de asistencia
    </x-slot>

    <p class="text-sm text-gray-600">
        Marca la asistencia de los alumnos que estuvieron presentes en esta reunión.
    </p>

    <ul class="list-disc list-inside text-sm text-gray-700 mt-3">
        <li>Solo se puede pasar lista si la reunión está activa.</li>
        <li>La asistencia se guarda por alumno y por reunión.</li>
        <li>Si un alumno no se marca, se registra como inasistencia.</li>
    </ul>

    <x-filament::section.description class="mt-4">
        ⚠️ Esta vista es informativa.  
        El formulario de asistencia se gestiona desde la acción del sistema.
    </x-filament::section.description>
</x-filament::section>
