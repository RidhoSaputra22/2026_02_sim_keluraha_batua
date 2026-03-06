<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add kelurahan_id to peta_layer_polygons
        if (! Schema::hasColumn('peta_layer_polygons', 'kelurahan_id')) {
            Schema::table('peta_layer_polygons', function (Blueprint $table) {
                $table->foreignId('kelurahan_id')->nullable()->after('rw_id')
                    ->constrained('kelurahans')->nullOnDelete();
            });
        }

        // 2. Create "Batas Kelurahan" layer in peta_layers (sort_order: 0 — always at bottom)
        $kelLayer = DB::table('peta_layers')->where('slug', 'batas-kelurahan')->first();
        if (! $kelLayer) {
            DB::table('peta_layers')->insert([
                'nama'         => 'Batas Kelurahan',
                'slug'         => 'batas-kelurahan',
                'deskripsi'    => 'Batas wilayah kelurahan',
                'warna'        => '#1e293b',
                'fill_opacity' => 0.02,
                'stroke_width' => 3.0,
                'pattern_type' => 'solid',
                'is_active'    => true,
                'sort_order'   => 0,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
            $kelLayerId = DB::table('peta_layers')->where('slug', 'batas-kelurahan')->value('id');
        } else {
            $kelLayerId = $kelLayer->id;
        }

        // 3. Copy kelurahan polygon(s) from kelurahans to peta_layer_polygons (PostGIS only)
        $isPostgres = DB::getDriverName() === 'pgsql';
        if ($kelLayerId && $isPostgres && Schema::hasColumn('kelurahans', 'polygon')) {
            $kelurahans = DB::select(
                'SELECT id, nama, ST_AsGeoJSON(polygon) as geojson FROM kelurahans WHERE polygon IS NOT NULL'
            );

            foreach ($kelurahans as $kel) {
                // Check if already migrated
                $exists = DB::table('peta_layer_polygons')
                    ->where('peta_layer_id', $kelLayerId)
                    ->where('kelurahan_id', $kel->id)
                    ->exists();

                if (! $exists && $kel->geojson) {
                    $polygonId = DB::table('peta_layer_polygons')->insertGetId([
                        'peta_layer_id' => $kelLayerId,
                        'kelurahan_id'  => $kel->id,
                        'nama'          => $kel->nama,
                        'deskripsi'     => 'Batas wilayah kelurahan ' . $kel->nama,
                        'warna'         => '#1e293b',
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);

                    // Copy PostGIS geometry
                    DB::statement(
                        'UPDATE peta_layer_polygons SET polygon = (SELECT polygon FROM kelurahans WHERE id = ?) WHERE id = ?',
                        [$kel->id, $polygonId]
                    );
                }
            }
        }

        // 4. Fix sort_order: kelurahan=0, RW=1, facility layers start at 10
        DB::table('peta_layers')->where('slug', 'batas-kelurahan')->update(['sort_order' => 0]);
        DB::table('peta_layers')->where('slug', 'wilayah-rw')->update(['sort_order' => 1]);
        // Facility layers already have sort_order 10+ from seeder, but ensure they're above RW
        DB::table('peta_layers')
            ->whereNotIn('slug', ['batas-kelurahan', 'wilayah-rw'])
            ->where('sort_order', '<', 10)
            ->update(['sort_order' => DB::raw('sort_order + 10')]);

        // 5. Drop polygon column from kelurahans (data now in peta_layer_polygons)
        if (Schema::hasColumn('kelurahans', 'polygon')) {
            Schema::table('kelurahans', function (Blueprint $table) {
                $table->dropColumn('polygon');
            });
        }
    }

    public function down(): void
    {
        $isPostgres = DB::getDriverName() === 'pgsql';

        // Re-add polygon column to kelurahans
        if (! Schema::hasColumn('kelurahans', 'polygon')) {
            if ($isPostgres) {
                DB::statement('ALTER TABLE kelurahans ADD COLUMN polygon geometry(MultiPolygon, 4326)');
            } else {
                Schema::table('kelurahans', fn (Blueprint $table) => $table->text('polygon')->nullable());
            }
        }

        // Copy polygons back (PostGIS only)
        $kelLayer = DB::table('peta_layers')->where('slug', 'batas-kelurahan')->first();
        if ($kelLayer && $isPostgres) {
            $polygons = DB::select(
                'SELECT kelurahan_id FROM peta_layer_polygons WHERE peta_layer_id = ? AND kelurahan_id IS NOT NULL AND polygon IS NOT NULL',
                [$kelLayer->id]
            );

            foreach ($polygons as $p) {
                DB::statement(
                    'UPDATE kelurahans SET polygon = (SELECT polygon FROM peta_layer_polygons WHERE peta_layer_id = ? AND kelurahan_id = ? LIMIT 1) WHERE id = ?',
                    [$kelLayer->id, $p->kelurahan_id, $p->kelurahan_id]
                );
            }
        }

        // Remove kelurahan layer data
        if ($kelLayer) {
            DB::table('peta_layer_polygons')->where('peta_layer_id', $kelLayer->id)->delete();
            DB::table('peta_layers')->where('slug', 'batas-kelurahan')->delete();
        }

        // Remove kelurahan_id column
        if (Schema::hasColumn('peta_layer_polygons', 'kelurahan_id')) {
            Schema::table('peta_layer_polygons', function (Blueprint $table) {
                $table->dropForeign(['kelurahan_id']);
                $table->dropColumn('kelurahan_id');
            });
        }
    }
};
