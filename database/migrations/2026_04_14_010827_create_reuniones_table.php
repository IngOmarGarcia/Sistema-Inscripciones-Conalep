<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reuniones', function (Blueprint $table) {
            $table->id();
            
            // Llave foránea que conecta con tu tabla groups
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            
            // Los campos que está pidiendo tu error
            $table->string('nombre');
            $table->date('fecha')->nullable(); // O usa dateTime('fecha') si necesitas hora exacta
            $table->boolean('activa')->default(0);
            
            // Esto crea automáticamente created_at y updated_at
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reuniones');
    }
};