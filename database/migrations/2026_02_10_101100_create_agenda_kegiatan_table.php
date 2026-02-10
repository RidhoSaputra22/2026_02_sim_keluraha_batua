<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('hari_kegiatan')->nullable();
            $table->string('jam', 20)->nullable();
            $table->string('lokasi')->nullable();
            $table->string('instansi_pengirim')->nullable();
            $table->string('perihal')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('arsip')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['hari_kegiatan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_kegiatan');
    }
};
