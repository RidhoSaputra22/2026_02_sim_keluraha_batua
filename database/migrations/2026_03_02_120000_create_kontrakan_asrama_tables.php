<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ═══════════════════════════════════════════════════════
        // Data Kontrakan dan Rumah Kost
        // ═══════════════════════════════════════════════════════
        Schema::create('kontrakans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelurahan_id')->nullable()->constrained('kelurahans')->nullOnDelete();
            $table->foreignId('rw_id')->nullable()->constrained('rws')->nullOnDelete();
            $table->foreignId('rt_id')->nullable()->constrained('rts')->nullOnDelete();
            $table->string('nama')->nullable();
            $table->string('alamat')->nullable();
            $table->string('pemilik')->nullable();
            $table->unsignedInteger('jumlah_kontrakan')->nullable()->default(0);
            $table->unsignedInteger('jumlah_kost_putera')->nullable()->default(0);
            $table->unsignedInteger('jumlah_kost_putri')->nullable()->default(0);
            $table->unsignedInteger('jumlah_kost_campur')->nullable()->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // ═══════════════════════════════════════════════════════
        // Data Asrama
        // ═══════════════════════════════════════════════════════
        Schema::create('asramas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelurahan_id')->nullable()->constrained('kelurahans')->nullOnDelete();
            $table->foreignId('rw_id')->nullable()->constrained('rws')->nullOnDelete();
            $table->foreignId('rt_id')->nullable()->constrained('rts')->nullOnDelete();
            $table->string('nama')->nullable();
            $table->string('alamat')->nullable();
            $table->string('jenis');  // TNI, POLRI, Mahasiswa, Kerukunan
            $table->unsignedInteger('jumlah')->nullable()->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asramas');
        Schema::dropIfExists('kontrakans');
    }
};
