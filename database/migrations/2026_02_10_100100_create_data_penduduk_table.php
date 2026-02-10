<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_penduduk', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20)->unique();
            $table->string('nama');
            $table->string('no_kk', 32)->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->text('alamat')->nullable();
            $table->enum('jenis_kelamin', ['L','P'])->nullable();
            $table->string('gol_darah', 3)->nullable();
            $table->string('agama')->nullable();
            $table->string('status_kawin')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('status_data')->nullable();
            $table->date('tgl_input')->nullable();
            $table->string('petugas_input')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('no_kk')->references('no_kk')->on('data_keluarga')->nullOnDelete()->cascadeOnUpdate();

            $table->index(['kelurahan','kecamatan']);
            $table->index(['rw','rt']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_penduduk');
    }
};
