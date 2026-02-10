<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_kendaraan', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_barang')->nullable();
            $table->string('nama_pengemudi')->nullable();
            $table->string('no_polisi', 20)->nullable();
            $table->string('no_rangka', 50)->nullable();
            $table->string('no_mesin', 50)->nullable();
            $table->unsignedInteger('tahun_perolehan')->nullable();
            $table->string('merek_type')->nullable();
            $table->string('wilayah_penugasan')->nullable();
            $table->string('arsip')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['no_polisi']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_kendaraan');
    }
};
