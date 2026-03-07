<?php

namespace App\Console\Commands;

use App\Models\PetaLayer;
use App\Models\PetaLayerPolygon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SimplifyPolygonsCommand extends Command
{
    protected $signature = 'polygon:simplify
                            {--tolerance=0.00005 : Toleransi simplifikasi dalam derajat (default ~5 meter). Lebih besar = lebih sederhana}
                            {--layer= : Slug atau ID layer yang akan disederhanakan. Kosong = semua layer}
                            {--dry-run : Tampilkan statistik tanpa menyimpan perubahan}';

    protected $description = 'Sederhanakan vertices polygon menggunakan ST_SimplifyPreserveTopology (PostGIS) tanpa merusak bentuk';

    public function handle(): int
    {
        if (DB::getDriverName() !== 'pgsql') {
            $this->error('Command ini hanya mendukung PostgreSQL/PostGIS.');
            $this->line('SQLite tidak memiliki fungsi ST_SimplifyPreserveTopology.');
            return self::FAILURE;
        }

        $tolerance = (float) $this->option('tolerance');
        $layerOption = $this->option('layer');
        $dryRun = $this->option('dry-run');

        if ($tolerance <= 0) {
            $this->error('Toleransi harus lebih besar dari 0.');
            return self::FAILURE;
        }

        $this->info("Toleransi simplifikasi: {$tolerance} derajat");
        if ($dryRun) {
            $this->warn('[DRY-RUN] Tidak ada perubahan yang disimpan.');
        }

        // Resolve layer filter
        $layerIds = null;
        if ($layerOption) {
            $layer = PetaLayer::where('slug', $layerOption)->orWhere('id', $layerOption)->first();
            if (! $layer) {
                $this->error("Layer tidak ditemukan: {$layerOption}");
                return self::FAILURE;
            }
            $layerIds = [$layer->id];
            $this->info("Layer target: {$layer->nama}");
        } else {
            $this->info('Layer target: semua layer');
        }

        // Fetch polygons that have a non-null polygon column
        $query = PetaLayerPolygon::query()
            ->select('peta_layer_polygons.id', 'peta_layer_polygons.nama', 'peta_layers.nama as layer_nama')
            ->join('peta_layers', 'peta_layers.id', '=', 'peta_layer_polygons.peta_layer_id')
            ->whereRaw('polygon IS NOT NULL')
            ->addSelect(DB::raw('ST_NPoints(polygon) as vertex_count'));

        if ($layerIds) {
            $query->whereIn('peta_layer_polygons.peta_layer_id', $layerIds);
        }

        $polygons = $query->get();

        if ($polygons->isEmpty()) {
            $this->warn('Tidak ada polygon ditemukan.');
            return self::SUCCESS;
        }

        $this->line("Ditemukan {$polygons->count()} polygon.\n");

        $headers = ['ID', 'Nama', 'Layer', 'Vertices Sebelum', 'Vertices Sesudah', 'Reduksi'];
        $rows = [];
        $totalBefore = 0;
        $totalAfter = 0;

        $bar = $this->output->createProgressBar($polygons->count());
        $bar->start();

        foreach ($polygons as $poly) {
            $verticesBefore = (int) $poly->vertex_count;

            // Preview: count vertices after simplification without saving
            $result = DB::selectOne(
                'SELECT ST_NPoints(ST_SimplifyPreserveTopology(polygon, ?)) as simplified_count
                 FROM peta_layer_polygons WHERE id = ?',
                [$tolerance, $poly->id]
            );

            $verticesAfter = (int) ($result->simplified_count ?? $verticesBefore);
            $reduction = $verticesBefore > 0
                ? round((1 - $verticesAfter / $verticesBefore) * 100, 1)
                : 0;

            $rows[] = [
                $poly->id,
                $poly->nama ?? '(tanpa nama)',
                $poly->layer_nama,
                number_format($verticesBefore),
                number_format($verticesAfter),
                "{$reduction}%",
            ];

            $totalBefore += $verticesBefore;
            $totalAfter += $verticesAfter;

            if (! $dryRun) {
                DB::statement(
                    'UPDATE peta_layer_polygons
                     SET polygon = ST_Multi(ST_SimplifyPreserveTopology(polygon, ?))
                     WHERE id = ?',
                    [$tolerance, $poly->id]
                );
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table($headers, $rows);

        $totalReduction = $totalBefore > 0
            ? round((1 - $totalAfter / $totalBefore) * 100, 1)
            : 0;

        $this->newLine();
        $this->info("Total vertices sebelum : " . number_format($totalBefore));
        $this->info("Total vertices sesudah : " . number_format($totalAfter));
        $this->info("Total reduksi          : {$totalReduction}%");

        if ($dryRun) {
            $this->newLine();
            $this->warn('[DRY-RUN] Jalankan tanpa --dry-run untuk menyimpan perubahan.');
        } else {
            $this->newLine();
            $this->info('✅ Simplifikasi polygon selesai.');
        }

        return self::SUCCESS;
    }
}
