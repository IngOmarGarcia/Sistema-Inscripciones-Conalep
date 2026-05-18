<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_taller', function (Blueprint $table) {
            $table->id();
            // Conecta con la tabla groups
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            // Conecta con la tabla talleres
            $table->foreignId('taller_id')->constrained('talleres')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_taller');
    }
};