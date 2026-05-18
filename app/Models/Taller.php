<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Taller extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'talleres';

    /**
     * Los atributos que se pueden asignar de forma masiva.
     */
    protected $fillable = [
        'nombre',
        'user_id',
    ];

    /**
     * Relación con el Maestro (User).
     * Vincula el taller con el usuario que tiene el rol de maestro.
     */
    public function encargado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con los Grupos.
     * Define qué grupos tienen acceso a este taller.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_taller');
    }

    /**
     * Relación con los Estudiantes (Adeudos).
     * Crucial para que aparezcan los detalles de material y observaciones.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'alumno_taller', 'taller_id', 'alumno_id')
            ->withPivot('debe_material', 'observaciones')
            ->withTimestamps();
    }
}