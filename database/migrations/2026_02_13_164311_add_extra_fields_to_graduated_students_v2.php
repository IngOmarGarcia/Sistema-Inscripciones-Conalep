<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('graduated_students', function (Blueprint $table) {
            // Campos que faltan según el error
            if (!Schema::hasColumn('graduated_students', 'genero')) {
                $table->string('genero')->nullable();
            }
            if (!Schema::hasColumn('graduated_students', 'direccion')) {
                $table->text('direccion')->nullable();
            }
            if (!Schema::hasColumn('graduated_students', 'career_id')) {
                $table->foreignId('career_id')->nullable();
            }
            if (!Schema::hasColumn('graduated_students', 'plan_estudios_cve')) {
                $table->string('plan_estudios_cve')->nullable();
            }
            if (!Schema::hasColumn('graduated_students', 'periodo_escolar_activo')) {
                $table->string('periodo_escolar_activo')->nullable();
            }
            if (!Schema::hasColumn('graduated_students', 'sit_de_estudios')) {
                $table->string('sit_de_estudios')->nullable();
            }
            if (!Schema::hasColumn('graduated_students', 'sit_academica')) {
                $table->string('sit_academica')->nullable();
            }
            if (!Schema::hasColumn('graduated_students', 'prom_pdo_escolar_anterior')) {
                $table->string('prom_pdo_escolar_anterior')->nullable();
            }
            if (!Schema::hasColumn('graduated_students', 'secundaria_cct')) {
                $table->string('secundaria_cct')->nullable();
            }
            if (!Schema::hasColumn('graduated_students', 'secundaria_prom')) {
                $table->string('secundaria_prom')->nullable();
            }
            // Agregamos otros que se ven en el SQL para evitar futuros errores
            $table->string('folio_p_prof')->nullable();
            $table->string('folio_serv_soc')->nullable();
            $table->integer('baja_alumno')->default(0);
            $table->string('situacion_curp')->nullable();
            $table->string('edad')->nullable();
            $table->string('nac_fecha_reg')->nullable();
            $table->string('email_institucional')->nullable();
            $table->string('folio_aspirante')->nullable();
            $table->string('etapa_asp')->nullable();
            $table->string('situacion_del_dato')->nullable();
            $table->string('doc_probatorio')->nullable();
            $table->string('dom_cv_asentamiento')->nullable();
            $table->string('medico_unidad_de_salud')->nullable();
            $table->string('medico_no_u_de_salud')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->boolean('encuesta_contestada')->default(false);
            $table->integer('tot_aciertos_exm')->nullable();
            $table->string('secundaria_nombre')->nullable();
            $table->string('group')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('graduated_students', function (Blueprint $table) {
            // No es estrictamente necesario para desarrollo local, 
            // pero podrías poner los dropColumn aquí.
        });
    }
};  