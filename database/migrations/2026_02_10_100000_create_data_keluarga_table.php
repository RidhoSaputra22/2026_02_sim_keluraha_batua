<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_keluarga', function (Blueprint $table) {
            $table->id();
            $table->string('no_kk', 32)->unique();
            $table->string('nama_kepala_keluarga');
            $table->unsignedInteger('jumlah_anggota_keluarga')->default(1);
            $table->string('rw', 5)->nullable();
            $table->string('rt', 5)->nullable();
            $table->date('tgl_input')->nullable();
            $table->string('petugas_input')->nullable();
            $table->string('arsip')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['rw','rt']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_keluarga');
    }
};
