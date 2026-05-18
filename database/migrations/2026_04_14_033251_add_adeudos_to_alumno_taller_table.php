<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumno_taller', function (Blueprint $table) {
            // Agregamos las columnas necesarias
            $table->boolean('debe_material')->default(false);
            $table->text('observaciones')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('alumno_taller', function (Blueprint $table) {
            // Permite revertir la migración si nos equivocamos
            $table->dropColumn(['debe_material', 'observaciones']);
        });
    }
};