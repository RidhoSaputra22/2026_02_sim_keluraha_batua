<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peta_layer_polygons', function (Blueprint $table) {
            $table->double('area')->nullable()->after('polygon');
        });
    }

    public function down(): void
    {
        Schema::table('peta_layer_polygons', function (Blueprint $table) {
            $table->dropColumn('area');
        });
    }
};
