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
        Schema::table('peta_layer_polygons', function (Blueprint $table) {
            $table->string('jenis', 20)->default('polygon')->after('peta_layer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peta_layer_polygons', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
    }
};
