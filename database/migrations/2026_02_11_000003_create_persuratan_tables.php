<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('surat_jenis', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('surat_sifat', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('instansis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('alamat')->nullable();
            $table->string('telp')->nullable();
            $table->timestamps();
        });

        Schema::create('layanan_publiks', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('pemohons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penduduk_id')->nullable()->constrained('penduduks')->nullOnDelete();
            $table->string('nama');
            $table->string('no_hp_wa')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('surats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelurahan_id')->constrained('kelurahans')->cascadeOnUpdate()->restrictOnDelete();

            $table->enum('arah', ['masuk','keluar']);
            $table->string('nomor_surat');
            $table->date('tanggal_surat')->nullable();
            $table->date('tanggal_diterima')->nullable();

            $table->foreignId('jenis_id')->nullable()->constrained('surat_jenis')->nullOnDelete();
            $table->foreignId('sifat_id')->nullable()->constrained('surat_sifat')->nullOnDelete();
            $table->foreignId('instansi_id')->nullable()->constrained('instansis')->nullOnDelete();
            $table->foreignId('layanan_publik_id')->nullable()->constrained('layanan_publiks')->nullOnDelete();

            $table->string('tujuan_surat')->nullable();
            $table->string('perihal')->nullable();
            $table->text('uraian')->nullable();

            $table->string('nama_dalam_surat')->nullable();
            $table->foreignId('pemohon_id')->nullable()->constrained('pemohons')->nullOnDelete();

            $table->enum('status_esign', ['draft','proses','signed','reject'])->nullable();

            $table->dateTime('tgl_input')->nullable();
            $table->foreignId('petugas_input_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('arsip_path')->nullable();

            $table->index(['kelurahan_id','arah']);
            $table->index(['nomor_surat']);
            $table->timestamps();
        });
    }

    public function down(): void
    {

        Schema::dropIfExists('surats');
        Schema::dropIfExists('pemohons');
        Schema::dropIfExists('layanan_publiks');
        Schema::dropIfExists('instansis');
        Schema::dropIfExists('surat_sifat');
        Schema::dropIfExists('surat_jenis');
    }
};
