<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_rekap_surat_keterangan_domisili', function (Blueprint $table) {
            $table->id();
            $table->string('kelurahan_desa')->nullable();
            $table->string('nama_layanan_publik')->nullable();
            $table->string('nama_pengguna_layanan')->nullable();
            $table->date('tgl_mengurus_layanan')->nullable();
            $table->string('no_hp_wa_aktif', 30)->nullable();
            $table->string('email_aktif')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['kelurahan_desa'], 'lrskd_kelurahan_desa_idx');
            $table->index(['tgl_mengurus_layanan'], 'lrskd_tgl_mengurus_layanan_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_rekap_surat_keterangan_domisili');
    }
};
