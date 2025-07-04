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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_perfil')->nullable();
            $table->unsignedBigInteger('id_permiso')->nullable();
            $table->string('name', 100);
            $table->string('email', 80)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 100);
            $table->json('ventas')->nullable(); //ventas del empleado en json
            $table->timestamps();
        });

        /* Schema::table('empleados',function (Blueprint $table){
            $table->foreign('id_detalle_compras')->references('id')->on('detalle_compras')->nullOnDelete();
            $table->foreign('id_perfil')->references('id')->on('perfiles')->nullOnDelete();
            $table->foreign('id_permiso')->references('id')->on('permisos')->nullOnDelete();
        }); */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
