<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Académicos
            $table->string('matricula')->nullable()->unique();
            $table->decimal('prom_gral', 4, 2)->nullable();
            $table->string('modelo_educativo')->nullable();
            $table->string('plan_estudios_cve')->nullable();
            $table->string('plan_estudios')->nullable();
            $table->string('periodo_escolar_activo')->nullable();
            $table->string('sit_de_estudios')->nullable();
            $table->string('sit_academica')->nullable();
            $table->decimal('prom_pdo_escolar_anterior', 4, 2)->nullable();
            $table->string('secundaria_cct')->nullable();
            $table->decimal('secundaria_prom', 4, 2)->nullable();
            $table->string('folio_p_prof')->nullable();
            $table->string('folio_serv_soc')->nullable();
            $table->boolean('baja_alumno')->default(false);

            // Personales Extra
            $table->string('situacion_curp')->nullable();
            $table->string('sexo')->nullable();
            $table->integer('edad')->nullable();
            $table->date('nac_fecha_reg')->nullable();
            $table->string('email_institucional')->nullable();
            $table->string('tel_celular')->nullable();
            $table->string('folio_aspirante')->nullable();
            $table->string('etapa_asp')->nullable();
            $table->string('situacion_del_dato')->nullable();
            $table->string('doc_probatorio')->nullable();

            // Domicilio
            $table->string('calle')->nullable();
            $table->string('dom_colonia')->nullable();
            $table->string('dom_cp')->nullable();
            $table->string('dom_ciudad')->nullable();
            $table->string('municipio')->nullable();
            $table->string('dom_ent_fed')->nullable();
            $table->string('dom_cv_asentamiento')->nullable();

            // Médicos
            $table->string('medico_institucion_medica')->nullable();
            $table->string('medico_unidad_de_salud')->nullable();
            $table->string('medico_no_u_de_salud')->nullable();
            $table->string('medico_cv_filiacion')->nullable();
            $table->string('medico_alergia')->nullable();
            $table->string('medico_cardiopatia')->nullable();
            $table->string('medico_epilepsia')->nullable();

            // Tutor
            $table->string('tutor_nombre')->nullable();
            $table->string('tutor_correo')->nullable();
            $table->string('tutor_telefono')->nullable();
            $table->string('tutor_celular')->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('students'); // O dropColumn si prefieres revertir uno a uno
    }
};