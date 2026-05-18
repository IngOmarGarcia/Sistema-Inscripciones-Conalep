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
    Schema::create('pagos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('student_id')->constrained()->onDelete('cascade');
        $table->string('folio');
        $table->string('periodo');
        $table->decimal('monto_total', 10, 2);
        $table->json('conceptos'); // Guardaremos los nombres de lo que pagó
        $table->timestamps();
    });
}
};
