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
        Schema::create('asistencia', function (Blueprint $table) {

            $table->id('id_asistencia');

            // Relación con empleado
            $table->integer('id_empleado');

            // Fecha del día laboral
            $table->date('fecha');

            // Hora de entrada
            $table->dateTime('hora_entrada');

            // Hora de salida
            $table->dateTime('hora_salida')->nullable();

            // Estado de jornada
            $table->enum('estado', [
                'Activa',
                'Finalizada'
            ])->default('Activa');

            $table->timestamps();
            $table->softDeletes();

            // Foreign key
            $table->foreign('id_empleado')
                ->references('id_empleado')
                ->on('empleado')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia');
    }
};