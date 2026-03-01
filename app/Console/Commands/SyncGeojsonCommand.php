<?php

namespace App\Console\Commands;

use App\Models\Kelurahan;
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

    protected $description = 'Sinkronisasi data GeoJSON ke kolom polygon di tabel rws dan kelurahans (PostGIS)';

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
     * Sync RW polygons from batua1.geojson.
     */
    private function syncRwPolygons(): void
    {
        $file = $this->option('rw-file');
        $disk = Storage::disk('public');

        if (! $disk->exists($file)) {
            $this->error("File RW GeoJSON tidak ditemukan: {$file}");
            return;
        }

        $geojson = json_decode($disk->get($file), true);

        if (! isset($geojson['features'])) {
            $this->error('Format GeoJSON RW tidak valid (features tidak ditemukan).');
            return;
        }

        $this->info('Sinkronisasi polygon RW...');
        $synced = 0;
        $skipped = 0;

        foreach ($geojson['features'] as $feature) {
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

            // Store polygon using PostGIS
            $geometryJson = json_encode($geometry);

            DB::statement(
                'UPDATE rws SET polygon = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) WHERE id = ?',
                [$geometryJson, $rw->id]
            );

            $this->line("  ✓ RW {$nomorRw} ({$rwName}) — polygon disimpan");
            $synced++;
        }

        $this->info("  Synced: {$synced}, Skipped: {$skipped}");
    }

    /**
     * Sync Kelurahan polygon from lurah.geojson.
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

            $geometryJson = json_encode($geometry);

            DB::statement(
                'UPDATE kelurahans SET polygon = ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) WHERE id = ?',
                [$geometryJson, $kelurahan->id]
            );

            $this->line("  ✓ Kelurahan {$kelurahan->nama} — polygon disimpan");
            $synced++;
        }

        $this->info("  Synced: {$synced}");
    }
}
