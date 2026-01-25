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
        Schema::table('votantes', function (Blueprint $table) {
            // Modificar latitud y longitud para soportar valores UTM y otros sistemas de coordenadas
            $table->decimal('latitud', 15, 6)->nullable()->change(); // 15 dígitos total, 6 decimales
            $table->decimal('longitud', 15, 6)->nullable()->change(); // 15 dígitos total, 6 decimales
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('votantes', function (Blueprint $table) {
            // Restaurar al formato original (solo funciona si no hay datos que excedan el límite)
            $table->decimal('latitud', 10, 7)->nullable()->change();
            $table->decimal('longitud', 10, 7)->nullable()->change();
        });
    }
};
