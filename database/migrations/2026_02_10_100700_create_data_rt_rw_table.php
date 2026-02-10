<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_rt_rw', function (Blueprint $table) {
            $table->id();
            $table->string('kelurahan')->nullable();
            $table->string('nik', 20)->nullable();
            $table->string('nama');
            $table->string('jabatan')->nullable(); // RT atau RW
            $table->string('rw', 5)->nullable();
            $table->string('rt', 5)->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->string('status')->nullable(); // aktif/nonaktif
            $table->text('alamat')->nullable();
            $table->string('no_telp', 30)->nullable();
            $table->string('no_rekening', 50)->nullable();
            $table->string('no_npwp', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('nik')->references('nik')->on('data_penduduk')->nullOnDelete()->cascadeOnUpdate();

            $table->index(['kelurahan','rw','rt']);
            $table->index(['jabatan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_rt_rw');
    }
};
