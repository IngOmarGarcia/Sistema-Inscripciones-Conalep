<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('talleres', function (Blueprint $table) {
            $table->id();
            
            // Agrega los campos que necesite tu taller, por ejemplo:
            $table->string('nombre');
            // Si el error original mencionaba un user_id en talleres, ponlo así:
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('talleres');
    }
};