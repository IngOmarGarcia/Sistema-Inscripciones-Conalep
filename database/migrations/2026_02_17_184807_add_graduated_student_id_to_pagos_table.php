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
        Schema::table('pagos', function (Blueprint $table) {
            // Añadimos la columna como nullable porque los alumnos activos no la tendrán llena
            // Se coloca después de student_id para mantener el orden lógico
            $table->foreignId('graduated_student_id')
                ->nullable()
                ->after('student_id') 
                ->constrained('graduated_students')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            // Es vital eliminar primero la llave foránea y luego la columna
            $table->dropForeign(['graduated_student_id']);
            $table->dropColumn('graduated_student_id');
        });
    }
};