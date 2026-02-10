<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai_staff', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 32)->unique();
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('gol', 10)->nullable();
            $table->string('pangkat')->nullable();
            $table->string('status_pegawai')->nullable();
            $table->date('tgl_input')->nullable();
            $table->string('petugas_input')->nullable();
            $table->unsignedInteger('no_urut')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['jabatan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai_staff');
    }
};
