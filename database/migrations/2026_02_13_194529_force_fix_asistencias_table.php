<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            // Agregamos reunion_id si no existe
            if (!Schema::hasColumn('asistencias', 'reunion_id')) {
                $table->foreignId('reunion_id')->nullable()->after('group_id')->constrained('reuniones')->cascadeOnDelete();
            }
            
            // Agregamos asistio como string para que acepte "No asistió"
            if (!Schema::hasColumn('asistencias', 'asistio')) {
                $table->string('asistio')->nullable()->after('reunion_id');
            }

            // Agregamos tutor_nombre si no existe
            if (!Schema::hasColumn('asistencias', 'tutor_nombre')) {
                $table->string('tutor_nombre')->nullable()->after('asistio');
            }
        });
    }

    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->dropForeign(['reunion_id']);
            $table->dropColumn(['reunion_id', 'asistio', 'tutor_nombre']);
        });
    }
};