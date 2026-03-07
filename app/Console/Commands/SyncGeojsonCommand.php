<?php

namespace App\Console\Commands;

use App\Models\Kelurahan;
use App\Models\PetaLayer;
use App\Models\PetaLayerPolygon;
use App\Models\Rw;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SyncGeojsonCommand extends Command
{
    protected $signature = 'geojson:sync
                            {--rw-file=geojson/batua1.geojson : Path to RW GeoJSON file in public disk}
                            {--kelurahan-file=geojson/lurah.geojson : Path to Kelurahan GeoJSON file in public disk}
                            {--only= : Sync only "rw" or "kelurahan"}';

    protected $description = 'Sinkronisasi data GeoJSON ke peta_layer_polygons (RW) dan kelurahans (PostGIS)';

    public function handle(): int
    {
        $only = $this->option('only');

        if (! $only || $only === 'rw') {
            $this->syncRwPolygons();
        }

        if (! $only || $only === 'kelurahan') {
            $this->syncKelurahanPolygons();
        }

        $this->info('✅ Sinkronisasi GeoJSON selesai.');

        return self::SUCCESS;
    }

    /**
     * Sync RW polygons from batua1.geojson into peta_layer_polygons (layer "wilayah-rw").
     */
    private function syncRwPolygons(): void
    {
        $file = $this->option('rw-file');
        $disk = Storage::disk('public');
        $polygonColor = [
            '#ff0000', // Merah
            '#00ff00', // Hijau
            '#0000ff', // Biru
            '#ffff00', // Kuning
            '#ff0000', // Merah
            '#00ff00', // Hijau
            '#0000ff', // Biru
            '#ffff00', // Kuning
            '#ff0000', // Merah
            '#00ff00', // Hijau
            '#0000ff', // Biru
            '#ffff00', // Kuning
        ];

        if (! $disk->exists($file)) {
            $this->error("File RW GeoJSON tidak ditemukan: {$file}");

            return;
        }

        $geojson = json_decode($disk->get($file), true);

        if (! isset($geojson['features'])) {
            $this->error('Format GeoJSON RW tidak valid (features tidak ditemukan).');

            return;
        }

        // Ensure the RW layer exists
        $rwLayer = PetaLayer::firstOrCreate(
            ['slug' => PetaLayer::LAYER_WILAYAH_RW],
            [
                'nama' => 'Wilayah RW',
                'deskripsi' => 'Batas wilayah RW',
                'warna' => '#6366f1',
                'fill_opacity' => 0.30,
                'stroke_width' => 2.5,
                'pattern_type' => 'solid',
                'is_active' => true,
                'sort_order' => 0,
            ]
        );

        $this->info('Sinkronisasi polygon RW...');
        $synced = 0;
        $skipped = 0;

        foreach ($geojson['features'] as $index => $feature) {
            $rwName = $feature['properties']['RW'] ?? null;
            $geometry = $feature['geometry'] ?? null;

            if (! $rwName || ! $geometry || empty($geometry['coordinates'])) {
                $skipped++;

                continue;
            }

            // Extract RW number from name like "RW 05" → 5
            $nomorRw = (int) preg_replace('/[^0-9]/', '', $rwName);

            if ($nomorRw <= 0) {
                $this->warn("  ⚠ Nomor RW tidak valid: {$rwName}");
                $skipped++;

                continue;
            }

            // Find the RW record
            $rw = Rw::where('nomor', $nomorRw)->first();

            if (! $rw) {
                $this->warn("  ⚠ RW {$nomorRw} tidak ditemukan di database, skip.");
                $skipped++;

                continue;
            }

            $warna = $polygonColor[$index % count($polygonColor)];
            $nama = 'RW '.str_pad($nomorRw, 2, '0', STR_PAD_LEFT);
            $geometryJson = json_encode($geometry);
            $jenis = strtolower($geometry['type'] ?? 'polygon');

            // Find or create polygon record in peta_layer_polygons
            $polygon = PetaLayerPolygon::firstOrCreate(
                ['peta_layer_id' => $rwLayer->id, 'rw_id' => $rw->id],
                ['nama' => $nama, 'warna' => $warna, 'jenis' => $jenis]
            );

            $polygon->update(['nama' => $nama, 'warna' => $warna, 'jenis' => $jenis]);

            // Store polygon geometry using PostGIS
            DB::statement(
                'UPDATE peta_layer_polygons SET polygon = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) WHERE id = ?',
                [$geometryJson, $polygon->id]
            );

            $this->line("  ✓ RW {$nomorRw} ({$rwName}) — polygon disimpan ke peta_layer_polygons");
            $synced++;
        }

        $this->info("  Synced: {$synced}, Skipped: {$skipped}");
    }

    /**
     * Sync Kelurahan polygon from lurah.geojson into peta_layer_polygons (layer "batas-kelurahan").
     */
    private function syncKelurahanPolygons(): void
    {
        $file = $this->option('kelurahan-file');
        $disk = Storage::disk('public');

        if (! $disk->exists($file)) {
            $this->error("File Kelurahan GeoJSON tidak ditemukan: {$file}");

            return;
        }

        $geojson = json_decode($disk->get($file), true);

        if (! isset($geojson['features'])) {
            $this->error('Format GeoJSON Kelurahan tidak valid (features tidak ditemukan).');

            return;
        }

        // Ensure the kelurahan layer exists
        $kelLayer = PetaLayer::firstOrCreate(
            ['slug' => PetaLayer::LAYER_BATAS_KELURAHAN],
            [
                'nama' => 'Batas Kelurahan',
                'deskripsi' => 'Batas wilayah kelurahan',
                'warna' => '#1e293b',
                'fill_opacity' => 0.02,
                'stroke_width' => 3.0,
                'pattern_type' => 'solid',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $this->info('Sinkronisasi polygon Kelurahan...');
        $synced = 0;

        // Kelurahan Batua — assume the first feature is the kelurahan boundary
        foreach ($geojson['features'] as $feature) {
            $geometry = $feature['geometry'] ?? null;

            if (! $geometry || empty($geometry['coordinates'])) {
                continue;
            }

            // Find Kelurahan Batua (or whatever kelurahan exists)
            $kelurahan = Kelurahan::where('nama', 'LIKE', '%Batua%')->first();

            if (! $kelurahan) {
                // Fallback: use the first kelurahan
                $kelurahan = Kelurahan::first();
            }

            if (! $kelurahan) {
                $this->warn('  ⚠ Tidak ada data kelurahan di database.');

                continue;
            }

            $nama = $kelurahan->nama;
            $geometryJson = json_encode($geometry);
            $jenis = strtolower($geometry['type'] ?? 'polygon');

            // Find or create polygon record in peta_layer_polygons
            $polygon = PetaLayerPolygon::firstOrCreate(
                ['peta_layer_id' => $kelLayer->id, 'kelurahan_id' => $kelurahan->id],
                ['nama' => $nama, 'deskripsi' => 'Batas wilayah kelurahan '.$nama, 'warna' => '#1e293b', 'jenis' => $jenis]
            );

            $polygon->update(['nama' => $nama, 'jenis' => $jenis]);

            // Store polygon geometry using PostGIS
            DB::statement(
                'UPDATE peta_layer_polygons SET polygon = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) WHERE id = ?',
                [$geometryJson, $polygon->id]
            );

            $this->line("  ✓ Kelurahan {$nama} — polygon disimpan ke peta_layer_polygons");
            $synced++;
        }

        $this->info("  Synced: {$synced}");

    }
}
