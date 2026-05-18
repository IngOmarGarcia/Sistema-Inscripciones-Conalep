<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Añadimos las columnas para conectar con Carreras y Grupos
            // Usamos nullable() por si hay alumnos que aún no tienen grupo
            $table->foreignId('career_id')->nullable()->constrained('careers')->nullOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Esto sirve para revertir los cambios si fuera necesario
            $table->dropForeign(['career_id']);
            $table->dropColumn('career_id');
            $table->dropForeign(['group_id']);
            $table->dropColumn('group_id');
        });
    }
};