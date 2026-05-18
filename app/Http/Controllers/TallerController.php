<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TallerController extends Controller
{
    public function index()
{
    if(auth()->user()->hasRole('Admin')){
        $talleres = Taller::with('alumnos')->get();
    }
    elseif(auth()->user()->hasRole('ServiciosEscolares')){
        $talleres = Taller::with('alumnos')->get();
    }
    elseif(auth()->user()->hasRole('EncargadoTaller')){
        $talleres = Taller::with('alumnos')
            ->where('user_id', auth()->id())
            ->get();
    }

    return view('talleres.index', compact('talleres'));
}


public function actualizarMaterial(Request $request)
{
    $taller = Taller::find($request->taller_id);

    if(auth()->user()->id !== $taller->user_id){
        abort(403);
    }

    $taller->alumnos()->updateExistingPivot(
        $request->alumno_id,
        [
            'debe_material' => $request->has('debe_material'),
            'observaciones' => $request->observaciones
        ]
    );

    return back()->with('success', 'Información actualizada');
}

public function show($id)
{
    $taller = Taller::with('alumnos')->findOrFail($id);

    // Si es encargado, solo puede ver su taller
    if(auth()->user()->hasRole('EncargadoTaller') 
       && $taller->user_id !== auth()->id()){
        abort(403);
    }

    return view('talleres.show', compact('taller'));
}

}
