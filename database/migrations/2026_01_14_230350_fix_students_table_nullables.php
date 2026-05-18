<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Hacemos que los campos viejos sean opcionales para que no bloqueen el guardado
            $table->string('email')->nullable()->change();
            $table->text('direccion')->nullable()->change();
            $table->string('genero')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
    }
};