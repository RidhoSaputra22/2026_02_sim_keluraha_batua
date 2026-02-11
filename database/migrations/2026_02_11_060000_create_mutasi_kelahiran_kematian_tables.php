<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mutasi Penduduk (pindah datang)
        Schema::create('mutasi_penduduks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penduduk_id')->constrained('penduduks')->cascadeOnDelete();
            $table->enum('jenis_mutasi', ['pindah', 'datang']);
            $table->date('tanggal_mutasi');
            $table->string('alamat_asal')->nullable();
            $table->string('alamat_tujuan')->nullable();
            $table->foreignId('rt_asal_id')->nullable()->constrained('rts')->nullOnDelete();
            $table->foreignId('rt_tujuan_id')->nullable()->constrained('rts')->nullOnDelete();
            $table->string('alasan')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('no_surat_pindah')->nullable();
            $table->string('status')->default('proses'); // proses, selesai, batal
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Kelahiran
        Schema::create('kelahirans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bayi');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir');
            $table->time('jam_lahir')->nullable();
            $table->foreignId('ibu_id')->nullable()->constrained('penduduks')->nullOnDelete();
            $table->foreignId('ayah_id')->nullable()->constrained('penduduks')->nullOnDelete();
            $table->foreignId('rt_id')->nullable()->constrained('rts')->nullOnDelete();
            $table->string('no_akte')->nullable();
            $table->string('keterangan')->nullable();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Kematian
        Schema::create('kematians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penduduk_id')->constrained('penduduks')->cascadeOnDelete();
            $table->date('tanggal_meninggal');
            $table->string('tempat_meninggal')->nullable();
            $table->string('penyebab')->nullable();
            $table->string('no_akte_kematian')->nullable();
            $table->string('keterangan')->nullable();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kematians');
        Schema::dropIfExists('kelahirans');
        Schema::dropIfExists('mutasi_penduduks');
    }
};
