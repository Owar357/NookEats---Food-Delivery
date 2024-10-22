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
        Schema::create('restaurantes', function (Blueprint $table) {
            $table->string('codigo_operacion',11)->primary();
            $table->string('ubicacion',255);
            $table->string('telefono',15)->unique();
            $table->string('telefono_secundario',15)->unique();
            $table->string('descripcion',300);
            $table->string('modelo_negocio',15);
            $table->enum('validacion_restaurante',['P','A','R'])->default('P');
            $table->unsignedBigInteger('tipo_negocio_id');
            $table->foreign('tipo_negocio_id')->references('id')->on('tipo_negocios');
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
        Schema::dropIfExists('restaurantes');
    }
};
