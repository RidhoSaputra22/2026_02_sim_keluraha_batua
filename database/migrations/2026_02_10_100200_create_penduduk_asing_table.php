<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penduduk_asing', function (Blueprint $table) {
            $table->id();
            $table->string('no_passport', 32)->unique();
            $table->string('nama');
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->text('alamat')->nullable();
            $table->enum('jenis_kelamin', ['L','P'])->nullable();
            $table->date('tgl_input')->nullable();
            $table->string('petugas_input')->nullable();
            $table->string('arsip')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['kelurahan','kecamatan']);
            $table->index(['rw','rt']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penduduk_asing');
    }
};
