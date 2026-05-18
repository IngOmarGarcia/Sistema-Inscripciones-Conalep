<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    use HasFactory;

    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'student_id',
        'graduated_student_id', // Permite vincular el pago al alumno graduado
        'folio',
        'periodo',
        'monto_total',
        'abono_inscripcion',
        'conceptos',
        'pdf_path',
    ];

    /**
     * Conversión de tipos de datos.
     */
    protected $casts = [
        'conceptos' => 'array',
        'monto_total' => 'float',
        'abono_inscripcion' => 'float',
    ];

    /**
     * Relación: Un pago pertenece a un Estudiante Activo.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relación: Un pago pertenece a un Estudiante Graduado.
     */
    public function graduatedStudent(): BelongsTo
    {
        return $this->belongsTo(GraduatedStudent::class, 'graduated_student_id');
    }
}