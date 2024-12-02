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
            $table->string('horario_dia',35);
            $table->boolean('abierto')->default(true);
            $table->unsignedBigInteger('restaurante_id');
            $table->foreign('restaurante_id')->references('id')->on('restaurantes');
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
