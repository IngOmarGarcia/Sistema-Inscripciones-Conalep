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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            
            // Datos personales
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno')->nullable();
            $table->string('curp', 18)->unique(); // La CURP siempre tiene 18 caracteres y es única
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['Hombre', 'Mujer', 'Otro'])->nullable();
            
            // Datos de contacto
            $table->string('email')->unique();
            $table->string('telefono')->nullable();
            $table->text('direccion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};