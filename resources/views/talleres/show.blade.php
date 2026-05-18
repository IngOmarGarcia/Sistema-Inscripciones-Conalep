@extends('layouts.app')

@section('content')

<h2>Alumnos del Taller: {{ $taller->nombre }}</h2>

<table class="table">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Material</th>
        </tr>
    </thead>
    <tbody>

    @foreach($taller->alumnos as $alumno)
    <tr>
        <td>{{ $alumno->nombre }}</td>

        <td>
            @role('EncargadoTaller')
            <form method="POST" action="{{ route('taller.actualizarMaterial') }}">
                @csrf
                <input type="hidden" name="alumno_id" value="{{ $alumno->id }}">
                <input type="hidden" name="taller_id" value="{{ $taller->id }}">

                <input type="checkbox" name="debe_material"
                    {{ $alumno->pivot->debe_material ? 'checked' : '' }}>

                <textarea name="observaciones">
                    {{ $alumno->pivot->observaciones }}
                </textarea>

                <button type="submit" class="btn btn-primary btn-sm">
                    Guardar
                </button>
            </form>
            @endrole

            @role('ServiciosEscolares')
                {{ $alumno->pivot->debe_material ? 'Sí' : 'No' }}
                <br>
                {{ $alumno->pivot->observaciones }}
            @endrole

            @role('Admin')
                {{ $alumno->pivot->debe_material ? 'Sí' : 'No' }}
                <br>
                {{ $alumno->pivot->observaciones }}
            @endrole

        </td>
    </tr>
    @endforeach

    </tbody>
</table>

@endsection
