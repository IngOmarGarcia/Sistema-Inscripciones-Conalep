<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'students';
    protected $guarded = [];

    // Accesor para el nombre completo
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}";
    }

    // Relación con Carrera
    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class, 'career_id');
    }

    // Relación con Grupo
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
    
    // Relación con Asistencias
    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class, 'student_id');
    }

    // Relación con Pagos
    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'student_id');
    }

    /**
     * Relación con Talleres (Adeudos)
     * Esta relación conecta al alumno con los talleres donde tiene un adeudo registrado.
     */
    public function talleres(): BelongsToMany
    {
        return $this->belongsToMany(
            Taller::class,
            'alumno_taller',
            'alumno_id',   
            'taller_id'    
        )
        ->withPivot('debe_material', 'observaciones')
        ->withTimestamps();
    }
}