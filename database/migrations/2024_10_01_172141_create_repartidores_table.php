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
        Schema::create('repartidores', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_vehiculo',200);
            $table->string('ubicacion_actual',200);
            $table->dateTime('fecha_contracion');
            $table->string('estado_repartidor')->default('inactivo');
            $table->string('disponibilidad',3);
            $table->string('placa',15);
            $table->string('numero_telefono',15);
            $table->boolean('validacion');
            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repartidores');
    }
};
