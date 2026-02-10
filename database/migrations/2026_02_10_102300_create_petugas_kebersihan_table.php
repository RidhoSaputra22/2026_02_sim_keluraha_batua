<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petugas_kebersihan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nik', 20)->nullable();
            $table->string('unit_kerja')->nullable();
            $table->enum('jenis_kelamin', ['L','P'])->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('nik')->references('nik')->on('data_penduduk')->nullOnDelete()->cascadeOnUpdate();

            $table->index(['unit_kerja']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petugas_kebersihan');
    }
};
