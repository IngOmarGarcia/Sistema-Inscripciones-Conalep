<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('graduated_students', function (Blueprint $table) {
            // Académicos adicionales
            $table->string('modelo_educativo')->nullable();
            $table->string('plan_estudios')->nullable();
            
            // Personales adicionales
            $table->string('sexo')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('tel_celular')->nullable();
            
            // Domicilio
            $table->string('calle')->nullable();
            $table->string('dom_colonia')->nullable();
            $table->string('dom_cp')->nullable();
            $table->string('dom_ciudad')->nullable();
            $table->string('municipio')->nullable();
            $table->string('dom_ent_fed')->nullable();
            
            // Médicos
            $table->string('medico_institucion_medica')->nullable();
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

    public function down(): void
    {
        Schema::table('graduated_students', function (Blueprint $table) {
            $table->dropColumn([
                'modelo_educativo', 'plan_estudios', 'sexo', 'fecha_nacimiento', 
                'tel_celular', 'calle', 'dom_colonia', 'dom_cp', 'dom_ciudad', 
                'municipio', 'dom_ent_fed', 'medico_institucion_medica', 
                'medico_cv_filiacion', 'medico_alergia', 'medico_cardiopatia', 
                'medico_epilepsia', 'tutor_nombre', 'tutor_correo', 
                'tutor_telefono', 'tutor_celular'
            ]);
        });
    }
};