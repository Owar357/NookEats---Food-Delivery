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
        Schema::create('comidas_restaurante', function (Blueprint $table) {
            $table->id();
            $table->decimal('precio',10,2);
            $table->nombre('nombre',125);
            $table->string('imagen',300);
            $table->decimal('precio',10,2);
            $table->string('decripcion',300);
            $table->boolean('disponibilidad');
            $table->boolean('promocion_activa');
            $table->decimal('descuento_porcentaje',5,2);
            $table->string('restaurante_id',11);
            $table->foreign('restaurante_id')->references('id')->on('restaurantes');    
            $table->unsignedBigInteger('categoria_id');
            $table->foreign('categoria_id')->references('id')->on('categorias');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comidas_restaurante');
    }
};
