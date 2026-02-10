<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_umkm', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pemilik');
            $table->string('nik', 20)->nullable();
            $table->string('no_hp', 30)->nullable();
            $table->string('nama_ukm');
            $table->text('alamat')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('sektor_umkm')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('nik')->references('nik')->on('data_penduduk')->nullOnDelete()->cascadeOnUpdate();

            $table->index(['sektor_umkm']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_umkm');
    }
};
