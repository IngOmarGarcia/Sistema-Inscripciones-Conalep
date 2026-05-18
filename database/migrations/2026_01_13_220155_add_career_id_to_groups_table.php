<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            // Agregamos la columna que falta y la conectamos con carreras
            $table->foreignId('career_id')->nullable()->after('name')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign(['career_id']);
            $table->dropColumn('career_id');
        });
    }
};