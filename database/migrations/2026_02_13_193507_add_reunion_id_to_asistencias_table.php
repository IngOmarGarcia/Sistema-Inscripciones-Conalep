<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            // Verificamos si las columnas existen antes de intentar crearlas
            if (!Schema::hasColumn('asistencias', 'reunion_id')) {
                // AQUÍ ESTÁ EL CAMBIO: 'reunions' en lugar de 'reuniones'
                $table->foreignId('reunion_id')->nullable()->constrained('reunions')->cascadeOnDelete();
            }
            
            if (!Schema::hasColumn('asistencias', 'asistio')) {
                $table->string('asistio')->nullable();
            }

            if (!Schema::hasColumn('asistencias', 'tutor_nombre')) {
                $table->string('tutor_nombre')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            if (Schema::hasColumn('asistencias', 'reunion_id')) {
                $table->dropForeign(['reunion_id']);
            }
            $table->dropColumn(['reunion_id', 'asistio', 'tutor_nombre']);
        });
    }
};