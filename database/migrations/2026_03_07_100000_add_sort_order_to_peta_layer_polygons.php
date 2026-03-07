<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peta_layer_polygons', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('properties');
        });

        // Set initial sort_order based on existing id order within each layer
        $layers = DB::table('peta_layer_polygons')
            ->select('peta_layer_id')
            ->distinct()
            ->pluck('peta_layer_id');

        foreach ($layers as $layerId) {
            $polygons = DB::table('peta_layer_polygons')
                ->where('peta_layer_id', $layerId)
                ->orderBy('id')
                ->pluck('id');

            foreach ($polygons as $index => $id) {
                DB::table('peta_layer_polygons')
                    ->where('id', $id)
                    ->update(['sort_order' => $index]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('peta_layer_polygons', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
