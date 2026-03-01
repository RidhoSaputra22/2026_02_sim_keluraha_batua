<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Enable PostGIS extension
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');

        // Add polygon column to rws table
        if (Schema::hasTable('rws') && ! Schema::hasColumn('rws', 'polygon')) {
            DB::statement('ALTER TABLE rws ADD COLUMN polygon geometry(MultiPolygon, 4326)');
        }

        // Add polygon column to kelurahans table
        if (Schema::hasTable('kelurahans') && ! Schema::hasColumn('kelurahans', 'polygon')) {
            DB::statement('ALTER TABLE kelurahans ADD COLUMN polygon geometry(MultiPolygon, 4326)');
        }

        // Create spatial index for faster queries
        DB::statement('CREATE INDEX IF NOT EXISTS rws_polygon_idx ON rws USING GIST (polygon)');
        DB::statement('CREATE INDEX IF NOT EXISTS kelurahans_polygon_idx ON kelurahans USING GIST (polygon)');
    }

    public function down(): void
    {
        if (Schema::hasColumn('rws', 'polygon')) {
            Schema::table('rws', function ($table) {
                $table->dropColumn('polygon');
            });
        }

        if (Schema::hasColumn('kelurahans', 'polygon')) {
            Schema::table('kelurahans', function ($table) {
                $table->dropColumn('polygon');
            });
        }

        DB::statement('DROP INDEX IF EXISTS rws_polygon_idx');
        DB::statement('DROP INDEX IF EXISTS kelurahans_polygon_idx');
    }
};
