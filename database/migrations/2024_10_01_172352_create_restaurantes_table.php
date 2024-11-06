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
            $table->id();
            $table->string('nombre',150);
            $table->string('codigo_operacion',11);
            $table->string('imagen',300);
            $table->string('ubicacion',255);
            $table->string('telefono',15)->unique();
            $table->string('telefono_secundario',15)->nullable();
            $table->string('descripcion',300);
            $table->string('modelo_negocio',15);
            $table->enum('validacion_restaurante',['P','A','R'])->default('P');
            $table->unsignedBigInteger('tipo_negocio_id');
            $table->foreign('tipo_negocio_id')->references('id')->on('tipo_negocios');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
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
