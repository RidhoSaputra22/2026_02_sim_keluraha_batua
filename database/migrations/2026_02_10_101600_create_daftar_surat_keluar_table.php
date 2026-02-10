<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_surat_keluar', function (Blueprint $table) {
            $table->id();
            $table->string('status_esign')->nullable();
            $table->string('jenis_surat')->nullable();
            $table->string('no_surat')->nullable();
            $table->date('tanggal_surat')->nullable();
            $table->string('nama_dalam_surat')->nullable();
            $table->string('nama_pemohon')->nullable();
            $table->string('no_telepon', 30)->nullable();
            $table->date('tgl_input')->nullable();
            $table->text('lainnya')->nullable();
            $table->string('petugas_input')->nullable();
            $table->string('arsip')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tanggal_surat']);
            $table->index(['jenis_surat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_surat_keluar');
    }
};
