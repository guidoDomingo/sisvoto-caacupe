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
        Schema::table('zonas', function (Blueprint $table) {
            $table->foreign('distrito_id')->references('id')->on('distritos')->nullOnDelete();
        });

        Schema::table('barrios', function (Blueprint $table) {
            $table->foreign('zona_id')->references('id')->on('zonas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zonas', function (Blueprint $table) {
            $table->dropForeign(['distrito_id']);
        });

        Schema::table('barrios', function (Blueprint $table) {
            $table->dropForeign(['zona_id']);
        });
    }
};
