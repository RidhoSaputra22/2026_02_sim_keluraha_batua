<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // ── Peta Layers (kategori layer: Banjir, Longsor, dll) ──
        Schema::create('peta_layers', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('slug', 100)->unique();
            $table->text('deskripsi')->nullable();
            $table->string('warna', 7)->default('#3b82f6');
            $table->decimal('fill_opacity', 3, 2)->default(0.30);
            $table->decimal('stroke_width', 3, 1)->default(2.0);
            $table->string('pattern_type', 20)->default('solid'); // solid, hatch, dots, crosshatch
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // ── Peta Layer Polygons (polygon individu per layer) ──
        Schema::create('peta_layer_polygons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peta_layer_id')->constrained('peta_layers')->cascadeOnDelete();
            $table->string('nama', 150)->nullable();
            $table->text('deskripsi')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
        });

        if ($driver === 'pgsql') {
            // Add PostGIS geometry column for layer polygons
            DB::statement('ALTER TABLE peta_layer_polygons ADD COLUMN polygon geometry(MultiPolygon, 4326)');
            DB::statement('CREATE INDEX IF NOT EXISTS peta_layer_polygons_polygon_idx ON peta_layer_polygons USING GIST (polygon)');
        } else {
            // SQLite / MySQL fallback: store polygon as TEXT
            Schema::table('peta_layer_polygons', fn ($table) => $table->text('polygon')->nullable());
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('peta_layer_polygons');
        Schema::dropIfExists('peta_layers');
    }
};
