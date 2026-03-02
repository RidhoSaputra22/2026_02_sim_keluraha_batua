<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan peta_layer_polygon_id ke tabel fasilitas
     * agar setiap row bisa ditampilkan sebagai titik/polygon di peta.
     *
     * Layer dikelompokkan per jenis data di tabel peta_layers:
     * - Sekolah
     * - Fasilitas Kesehatan
     * - Tempat Ibadah
     * - Kontrakan & Kost
     * - Asrama
     * - Data Usaha (UMKM)
     */
    public function up(): void
    {
        $tables = [
            'sekolahs',
            'faskes',
            'tempat_ibadahs',
            'kontrakans',
            'asramas',
            'umkms',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->foreignId('peta_layer_polygon_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('peta_layer_polygons')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'sekolahs',
            'faskes',
            'tempat_ibadahs',
            'kontrakans',
            'asramas',
            'umkms',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) use ($table) {
                $t->dropConstrainedForeignId('peta_layer_polygon_id');
            });
        }
    }
};
