<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ubah tipe kolom polygon dari MultiPolygon ke Geometry agar bisa
     * menyimpan Point (fasilitas/usaha), Polygon, maupun MultiPolygon.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE peta_layer_polygons ALTER COLUMN polygon TYPE geometry(Geometry, 4326) USING polygon::geometry(Geometry, 4326)');
        }
        // SQLite/MySQL: kolom sudah TEXT, tidak perlu diubah
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            // Kembalikan ke MultiPolygon (data Point akan hilang)
            DB::statement('ALTER TABLE peta_layer_polygons ALTER COLUMN polygon TYPE geometry(MultiPolygon, 4326) USING polygon::geometry(MultiPolygon, 4326)');
        }
    }
};
