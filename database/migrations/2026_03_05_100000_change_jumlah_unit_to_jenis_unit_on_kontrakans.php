<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kontrakans', function (Blueprint $table) {
            // Hapus kolom jumlah unit lama
            $table->dropColumn([
                'jumlah_kontrakan',
                'jumlah_kost_putera',
                'jumlah_kost_putri',
                'jumlah_kost_campur',
            ]);

            // Tambah kolom jenis_unit dan jumlah_kamar
            $table->string('jenis_unit')->nullable()->after('pemilik');
            $table->unsignedInteger('jumlah_kamar')->nullable()->default(0)->after('jenis_unit');
        });
    }

    public function down(): void
    {
        Schema::table('kontrakans', function (Blueprint $table) {
            $table->dropColumn(['jenis_unit', 'jumlah_kamar']);

            // Kembalikan kolom lama
            $table->unsignedInteger('jumlah_kontrakan')->nullable()->default(0)->after('pemilik');
            $table->unsignedInteger('jumlah_kost_putera')->nullable()->default(0)->after('jumlah_kontrakan');
            $table->unsignedInteger('jumlah_kost_putri')->nullable()->default(0)->after('jumlah_kost_putera');
            $table->unsignedInteger('jumlah_kost_campur')->nullable()->default(0)->after('jumlah_kost_putri');
        });
    }
};
