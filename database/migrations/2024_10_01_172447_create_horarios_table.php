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
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->time('hora_apertura');
            $table->time('hora_cierre');
            $table->boolean('abierto');
            $table->unsignedBigInteger('horario_dia_id');
            $table->foreign('horario_dia_id')->references('id')->on('horarios_dias');
            $table->string('codigo_operacion',11);
            $table->foreign('codigo_operacion')->references('codigo_operacion')->on('restaurantes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
