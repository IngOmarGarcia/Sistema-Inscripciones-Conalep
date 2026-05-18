<?php

use Illuminate\Support\Facades\Route;
use App\Models\Group;
use App\Http\Controllers\TallerController; 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Al entrar a http://127.0.0.1:8000, el sistema te enviará a /admin.
// Si no has iniciado sesión, Filament te mostrará el login automáticamente.
Route::redirect('/', '/admin');

Route::get('/test-view', function () {
    dd(view()->exists('filament.reuniones.pasar-lista'));
});

Route::get('/grupos/{group}/asistencia', function (Group $group) {
    return view('filament.grupos.asistencia', [
        'group' => $group->load(['students', 'reuniones', 'asistencias'])
    ]);
})->name('grupos.asistencia');


Route::get('/talleres/{id}', [TallerController::class, 'show'])
    ->name('talleres.show');

Route::post('/talleres/material', [TallerController::class, 'actualizarMaterial'])
    ->name('taller.actualizarMaterial');


// --- NUEVA RUTA PARA VER LOS PDF SIN ERROR 403 ---
Route::get('/documentos/{path}', function ($path) {
    // Busca el archivo directamente en la carpeta storage/app/public/
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        abort(404, 'El documento PDF no existe en el servidor.');
    }

    return response()->file($fullPath);
})->name('ver.pdf')->where('path', '.*');