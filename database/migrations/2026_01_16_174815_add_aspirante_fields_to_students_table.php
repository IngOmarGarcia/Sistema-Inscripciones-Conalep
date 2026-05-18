<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Verificamos cada campo antes de intentar crearlo
            if (!Schema::hasColumn('students', 'folio_aspirante')) {
                $table->string('folio_aspirante')->nullable()->after('matricula');
            }
            if (!Schema::hasColumn('students', 'etapa_asp')) {
                $table->string('etapa_asp')->nullable()->after('folio_aspirante');
            }
            if (!Schema::hasColumn('students', 'encuesta_contestada')) {
                $table->boolean('encuesta_contestada')->default(false);
            }
            if (!Schema::hasColumn('students', 'tot_aciertos_exm')) {
                $table->integer('tot_aciertos_exm')->nullable();
            }
            if (!Schema::hasColumn('students', 'plan_estudios_cve')) {
                $table->string('plan_estudios_cve')->nullable();
            }
            if (!Schema::hasColumn('students', 'periodo_escolar_activo')) {
                $table->string('periodo_escolar_activo')->nullable();
            }
            if (!Schema::hasColumn('students', 'modelo_educativo')) {
                $table->string('modelo_educativo')->nullable();
            }
            if (!Schema::hasColumn('students', 'secundaria_nombre')) {
                $table->string('secundaria_nombre')->nullable();
            }
            if (!Schema::hasColumn('students', 'secundaria_cct')) {
                $table->string('secundaria_cct')->nullable();
            }
            if (!Schema::hasColumn('students', 'secundaria_prom')) {
                $table->decimal('secundaria_prom', 4, 2)->nullable();
            }
            if (!Schema::hasColumn('students', 'dom_cp')) {
                $table->string('dom_cp')->nullable();
            }
            if (!Schema::hasColumn('students', 'dom_colonia')) {
                $table->string('dom_colonia')->nullable();
            }
            if (!Schema::hasColumn('students', 'dom_ent_fed')) {
                $table->string('dom_ent_fed')->nullable();
            }
            if (!Schema::hasColumn('students', 'municipio')) {
                $table->string('municipio')->nullable();
            }
            if (!Schema::hasColumn('students', 'telefono')) {
                $table->string('telefono')->nullable();
            }
            if (!Schema::hasColumn('students', 'tel_celular')) {
                $table->string('tel_celular')->nullable();
            }
        });
    }

    public function down(): void
    {
        // No es necesario borrar si estamos parchando, pero puedes dejarlo vacío
    }
};