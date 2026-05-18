<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            // 1. Borramos la relación incorrecta ("reunions")
            $table->dropForeign(['reunion_id']);
            
            // 2. Creamos la relación correcta apuntando a "reuniones"
            $table->foreign('reunion_id')
                  ->references('id')
                  ->on('reuniones')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            $table->dropForeign(['reunion_id']);
            $table->foreign('reunion_id')->references('id')->on('reunions')->cascadeOnDelete();
        });
    }
};