<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan latitude & longitude ke tabel fasilitas
     * yang belum memiliki kolom koordinat (sekolahs sudah punya).
     */
    public function up(): void
    {
        $tables = ['faskes', 'tempat_ibadahs', 'kontrakans', 'asramas', 'umkms'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->decimal('latitude', 10, 7)->nullable()->after('alamat');
                $t->decimal('longitude', 10, 7)->nullable()->after('latitude');
            });
        }
    }

    public function down(): void
    {
        $tables = ['faskes', 'tempat_ibadahs', 'kontrakans', 'asramas', 'umkms'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn(['latitude', 'longitude']);
            });
        }
    }
};
