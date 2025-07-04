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
        Schema::create('perfiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_empleado')->constrained('empleados');
            $table->string('foto_perfil', 100)->nullable();
            $table->timestamp('fecha_creacion')->nullable();
            $table->timestamps();
        });

        /* Schema::table('perfiles', function (Blueprint $table) {
            $table->foreign('id_empleado')->references('id')->on('empleados')->nullOnDelete();
        }); */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfiles');
    }
};
