<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reunion extends Model
{
    protected $table = 'reuniones';

    protected $fillable = [
        'group_id',
        'nombre',
        'fecha',
        'activa',
    ];

    protected $casts = [
        'fecha' => 'datetime:Y-m-d H:i:s',
    ];


    /**
     * RELACIONES
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }

    /**
     * Garantiza una sola reunión activa por grupo
     */
    protected static function booted()
{
    static::saving(function ($reunion) {
        if ($reunion->activa) {
            static::where('group_id', $reunion->group_id)
                ->where('id', '!=', $reunion->id)
                ->update(['activa' => false]);
        }
    });
}

}
