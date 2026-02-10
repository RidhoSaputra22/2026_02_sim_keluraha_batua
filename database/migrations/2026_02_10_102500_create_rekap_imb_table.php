<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekap_imb', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pemohon')->nullable();
            $table->text('alamat_pemohon')->nullable();
            $table->text('alamat_bangunan')->nullable();
            $table->string('status_luas_tanah')->nullable();
            $table->string('nama_pada_surat')->nullable();
            $table->string('penggunaan_fungsi_gedung')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_imb');
    }
};
