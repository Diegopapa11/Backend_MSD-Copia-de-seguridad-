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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_empleado')->nullable();
            $table->unsignedBigInteger('id_detalle_compras')->nullable();
            $table->timestamp('fecha_compra')->useCurrent();
            $table->unsignedTinyInteger('estado')->default(1);
            $table->unsignedBigInteger('metodo_pago_id')->nullable();
            $table->timestamps();
        });

        /* Schema::table('compras', function (Blueprint $table) {
            $table->foreign('id_empleado')->references('id')->on('empleados')->nullOnDelete();
            $table->foreign('id_cliente')->references('id')->on('clientes')->nullOnDelete();
            $table->foreign('id_producto')->references('id')->on('productos')->nullOnDelete();
            $table->foreign('metood_pago_id')->references('id')->on('metodo_pagos')->nullOnDelete();
        }); */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
