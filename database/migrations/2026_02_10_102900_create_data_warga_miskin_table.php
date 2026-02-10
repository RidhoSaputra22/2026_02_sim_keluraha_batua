<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_warga_miskin', function (Blueprint $table) {
            $table->id();
            $table->string('kelurahan')->nullable();
            $table->string('nama')->nullable();
            $table->string('nik', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('no_peserta')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('nik')->references('nik')->on('data_penduduk')->nullOnDelete()->cascadeOnUpdate();

            $table->index(['kelurahan']);
            $table->index(['rw','rt']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_warga_miskin');
    }
};
