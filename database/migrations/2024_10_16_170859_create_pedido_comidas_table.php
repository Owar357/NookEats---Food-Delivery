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
        Schema::create('pedido_comidas', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad');
            $table->text('nota')->nullable();
            $table ->string('nombre_comida',200);
            $table ->decimal('precio',10,2);
            $table->decimal('precio_descuento',10,2)->default(0.00);
            $table->boolean('promocion_activa');
            $table->unsignedBigInteger('pedido_id');
            $table->foreign('pedido_id')->references('id')->on('pedidos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_comidas');
    }
};
