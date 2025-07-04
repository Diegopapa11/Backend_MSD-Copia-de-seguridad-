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
        Schema::create('perfil_x__permisos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_perfil')->constrained('perfiles');
            $table->unsignedBigInteger('id_permisos')->constrained('permisos');
            $table->timestamps();
        });

        /* Schema::table('perfil_x__permisos', function (Blueprint $table) {
            $table->foreign('id_perfil')->references('id')->on('perfiles')->nullOnDelete();
            $table->foreign('id_permiso')->references('id')->on('permisos')->nullOnDelete();
        }); */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfil_x__permisos');
    }
};
