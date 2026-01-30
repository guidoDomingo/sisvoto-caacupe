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
        Schema::create('zonas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('codigo', 10)->nullable()->unique();
            $table->unsignedBigInteger('distrito_id')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('color', 7)->default('#3B82F6'); // Color hex para mapas
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->index(['distrito_id', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zonas');
    }
};
