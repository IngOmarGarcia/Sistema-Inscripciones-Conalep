<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Reunion;

class Group extends Model
{
    use HasFactory;

    // Añadimos 'career_id' a la lista para que Laravel permita guardar ese dato
    protected $fillable = ['name', 'turno', 'career_id', 'tutor_id'];

    protected static function booted()
    {
        static::created(function ($group) {
            for ($i = 1; $i <= 3; $i++) {
                Reunion::create([
                    'group_id' => $group->id,
                    'nombre'   => "Reunión $i",
                    'fecha'    => null,
                    'activa'   => false,
                ]);
            }
        });
    }

    /**
     * Relación: Un grupo pertenece a una carrera.
     * Esto corrige el error TypeError en Filament
     */
    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    /**
     * Relación: Un grupo tiene muchos alumnos.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Relación: Un grupo tiene muchas asistencias a través de sus alumnos.
     * (ESTO CORRIGE EL ERROR DEL SERVIDOR 500)
     */
    public function asistencias(): HasManyThrough
    {
        return $this->hasManyThrough(Asistencia::class, Student::class);
    }

    public function reuniones(): HasMany
    {
        return $this->hasMany(Reunion::class);
    }

    public function activeReunion(): ?Reunion
    {
        return $this->reuniones()->where('activa', true)->first();
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    /**
     * Relación: Un grupo tiene muchos talleres.
     */
    public function talleres(): BelongsToMany
    {
        return $this->belongsToMany(
            Taller::class, 
            'group_taller', 
            'group_id', 
            'taller_id'
        )->withTimestamps();
    }
}