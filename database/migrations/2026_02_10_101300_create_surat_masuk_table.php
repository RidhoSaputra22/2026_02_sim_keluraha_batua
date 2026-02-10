<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat')->nullable();
            $table->string('jenis_surat')->nullable();
            $table->string('sifat_surat')->nullable();
            $table->string('asal_surat')->nullable();
            $table->date('tanggal_diterima')->nullable();
            $table->string('arsip')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tanggal_diterima']);
            $table->index(['jenis_surat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_masuk');
    }
};
