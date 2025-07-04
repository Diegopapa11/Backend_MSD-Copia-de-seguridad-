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
        Schema::create('detalle_compras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_compra')->nullable();
            $table->unsignedBigInteger('id_cliente')->nullable();
            $table->integer('cantidad');
            $table->decimal('precio', 10, 2);
            $table->timestamps();
        });

        /* Schema::table('detalle_compras', function (Blueprint $table) {
            $table->foreign('id_compra')->references('id')->on('compras')->nullOnDelete();
            $table->foreign('id_cliente')->references('id')->on('clientes')->nullOnDelete();
        }); */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_compras');
    }
};
