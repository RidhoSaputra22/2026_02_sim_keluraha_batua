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

        // 1. Add warna and rw_id columns to peta_layer_polygons
        Schema::table('peta_layer_polygons', function (Blueprint $table) {
            $table->string('warna', 7)->nullable()->after('deskripsi');
            $table->unsignedBigInteger('rw_id')->nullable()->after('warna');

            $table->foreign('rw_id')->references('id')->on('rws')->nullOnDelete();
        });

        // 2. Create the "RW" parent layer in peta_layers
        $rwLayerId = DB::table('peta_layers')->insertGetId([
            'nama'         => 'Wilayah RW',
            'slug'         => 'wilayah-rw',
            'deskripsi'    => 'Batas wilayah RW',
            'warna'        => '#6366f1',
            'fill_opacity' => 0.30,
            'stroke_width' => 2.5,
            'pattern_type' => 'solid',
            'is_active'    => true,
            'sort_order'   => 1,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // 3. Migrate existing RW polygons → peta_layer_polygons
        if ($driver === 'pgsql') {
            // PostGIS: copy geometry directly
            DB::statement("
                INSERT INTO peta_layer_polygons (peta_layer_id, nama, deskripsi, warna, rw_id, polygon, created_at, updated_at)
                SELECT
                    ?,
                    CONCAT('RW ', LPAD(nomor::text, 2, '0')),
                    deskripsi,
                    warna,
                    id,
                    polygon,
                    NOW(), NOW()
                FROM rws
                WHERE polygon IS NOT NULL
            ", [$rwLayerId]);
        } else {
            // SQLite / MySQL fallback: copy text data
            $rows = DB::table('rws')->whereNotNull('polygon')->get();
            foreach ($rows as $rw) {
                DB::table('peta_layer_polygons')->insert([
                    'peta_layer_id' => $rwLayerId,
                    'nama'          => 'RW ' . str_pad($rw->nomor, 2, '0', STR_PAD_LEFT),
                    'deskripsi'     => $rw->deskripsi ?? null,
                    'warna'         => $rw->warna ?? null,
                    'rw_id'         => $rw->id,
                    'polygon'       => $rw->polygon,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }

        // 4. Drop polygon & warna columns from rws
        if ($driver === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS rws_polygon_idx');
        }

        Schema::table('rws', function (Blueprint $table) {
            $table->dropColumn(['polygon', 'warna']);
        });
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // Re-add columns to rws
        if ($driver === 'pgsql') {
            if (!Schema::hasColumn('rws', 'polygon')) {
                DB::statement('ALTER TABLE rws ADD COLUMN polygon geometry(MultiPolygon, 4326)');
            }
            if (!Schema::hasColumn('rws', 'warna')) {
                Schema::table('rws', fn (Blueprint $table) => $table->string('warna', 7)->nullable()->after('nomor'));
            }
            DB::statement('CREATE INDEX IF NOT EXISTS rws_polygon_idx ON rws USING GIST (polygon)');

            // Copy polygons back from peta_layer_polygons
            $rwLayer = DB::table('peta_layers')->where('slug', 'wilayah-rw')->first();
            if ($rwLayer) {
                DB::statement("
                    UPDATE rws SET
                        polygon = plp.polygon,
                        warna = plp.warna
                    FROM peta_layer_polygons plp
                    WHERE plp.rw_id = rws.id AND plp.peta_layer_id = ?
                ", [$rwLayer->id]);
            }
        } else {
            if (!Schema::hasColumn('rws', 'polygon')) {
                Schema::table('rws', fn (Blueprint $table) => $table->text('polygon')->nullable());
            }
            if (!Schema::hasColumn('rws', 'warna')) {
                Schema::table('rws', fn (Blueprint $table) => $table->string('warna', 7)->nullable()->after('nomor'));
            }

            // Copy data back
            $rwLayer = DB::table('peta_layers')->where('slug', 'wilayah-rw')->first();
            if ($rwLayer) {
                $polygons = DB::table('peta_layer_polygons')
                    ->where('peta_layer_id', $rwLayer->id)
                    ->whereNotNull('rw_id')
                    ->get();
                foreach ($polygons as $p) {
                    DB::table('rws')->where('id', $p->rw_id)->update([
                        'polygon' => $p->polygon,
                        'warna'   => $p->warna,
                    ]);
                }
            }
        }

        // Remove the RW layer & its polygons
        DB::table('peta_layers')->where('slug', 'wilayah-rw')->delete();

        // Drop added columns from peta_layer_polygons
        Schema::table('peta_layer_polygons', function (Blueprint $table) {
            $table->dropForeign(['rw_id']);
            $table->dropColumn(['warna', 'rw_id']);
        });
    }
};
