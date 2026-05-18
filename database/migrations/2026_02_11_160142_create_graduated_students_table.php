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
        Schema::create('graduated_students', function (Blueprint $table) {
            $table->id();
            
            // Datos Académicos
            $table->string('matricula')->unique();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->string('periodo_egreso')->nullable(); // Ej: 2022-2025
            $table->string('folio_certificacion')->nullable();
            $table->decimal('prom_gral', 4, 2)->nullable();
            
            // Datos Personales
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno')->nullable();
            $table->string('curp', 18)->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            
            // Datos Administrativos (Para el Badge de Pago)
            $table->decimal('monto_pagado', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('graduated_students');
    }
};