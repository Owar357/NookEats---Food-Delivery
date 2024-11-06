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
            $table->decimal('precioDescuento',10,2)->default(0.00);
            $table->string('nombre',125);
            $table->string('imagen',300);
            $table->string('descripcion',300);
            $table->boolean('disponibilidad')->default(true);
            $table->boolean('promocion_activa')->default(false);
            $table->date('fecha_promfinal')->nullable();
            $table->decimal('descuento',5,2)->nullable();
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
