<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('agenda_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelurahan_id')->constrained('kelurahans')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('hari_kegiatan');
            $table->string('jam', 50)->nullable();
            $table->string('lokasi')->nullable();
            $table->foreignId('instansi_id')->nullable()->constrained('instansis')->nullOnDelete();
            $table->string('perihal')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('arsip_path')->nullable();
            $table->timestamps();
        });

        Schema::create('hasil_kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_id')->constrained('agenda_kegiatans')->cascadeOnUpdate()->cascadeOnDelete();
            $table->dateTime('hari_tanggal')->nullable();
            $table->longText('notulen')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('arsip_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {

        Schema::dropIfExists('hasil_kegiatans');
        Schema::dropIfExists('agenda_kegiatans');
    }
};
