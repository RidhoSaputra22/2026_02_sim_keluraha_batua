<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_rekap_surat_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('kelurahan_desa')->nullable();
            $table->string('no_surat')->nullable();
            $table->string('jenis_surat')->nullable();
            $table->string('sifat_surat')->nullable();
            $table->string('asal_surat')->nullable();
            $table->date('tanggal_diterima')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['kelurahan_desa']);
            $table->index(['tanggal_diterima']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_rekap_surat_masuk');
    }
};
