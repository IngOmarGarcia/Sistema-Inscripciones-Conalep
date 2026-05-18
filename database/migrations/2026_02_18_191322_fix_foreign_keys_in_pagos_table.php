<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            // 1. Eliminamos la restricción de cascada vieja
            // Nota: El nombre del índice suele ser 'pagos_student_id_foreign'
            $table->dropForeign(['student_id']);

            // 2. La volvemos a crear pero que permita NULOS y sin borrar en cascada
            $table->foreignId('student_id')
                ->nullable()
                ->change() // Requiere haber instalado: composer require doctrine/dbal
                ->constrained()
                ->onDelete('set null'); 
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->foreignId('student_id')->nullable(false)->change()->constrained()->onDelete('cascade');
        });
    }
};