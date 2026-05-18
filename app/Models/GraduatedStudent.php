<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GraduatedStudent extends Model
{
    use HasFactory;

    // Especificamos la tabla explícitamente
    protected $table = 'graduated_students';

    // Permitimos la asignación masiva para todos los campos
    protected $guarded = [];

    // Aseguramos que los tipos de datos sean correctos al consultarlos
    protected $casts = [
        'monto_pagado' => 'float',
        'prom_gral' => 'float',
        'fecha_nacimiento' => 'date', // Mantén esto si tu tabla de graduados ya tiene esta columna
    ];

    /**
     * Relación: Un alumno graduado pertenece a un grupo.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * Relación: Un alumno graduado tiene un historial de pagos.
     */
    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'graduated_student_id');
    }
}