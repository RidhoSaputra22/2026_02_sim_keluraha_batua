<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rts', function (Blueprint $table) {
            $table->index('rw_id', 'idx_rt_rw');
        });

        Schema::table('penduduks', function (Blueprint $table) {
            $table->index('rt_id', 'idx_penduduk_rt');
            $table->index('jenis_kelamin', 'idx_penduduk_jk');
        });

        Schema::table('keluargas', function (Blueprint $table) {
            $table->index('rt_id', 'idx_keluarga_rt');
        });

        Schema::table('umkms', function (Blueprint $table) {
            $table->index('rt_id', 'idx_umkm_rt');
        });
    }

    public function down(): void
    {
        Schema::table('rts', function (Blueprint $table) {
            $table->dropIndex('idx_rt_rw');
        });

        Schema::table('penduduks', function (Blueprint $table) {
            $table->dropIndex('idx_penduduk_rt');
            $table->dropIndex('idx_penduduk_jk');
        });

        Schema::table('keluargas', function (Blueprint $table) {
            $table->dropIndex('idx_keluarga_rt');
        });

        Schema::table('umkms', function (Blueprint $table) {
            $table->dropIndex('idx_umkm_rt');
        });
    }
};
