<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('pegawai_staff', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('gol')->nullable();
            $table->string('pangkat')->nullable();
            $table->string('status_pegawai')->nullable();
            $table->dateTime('tgl_input')->nullable();
            $table->foreignId('petugas_input_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('no_urut')->nullable();
            $table->timestamps();
        });

        Schema::create('penandatanganans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai_staff')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('status')->nullable();
            $table->string('no_telp')->nullable();
            $table->dateTime('tgl_input')->nullable();
            $table->foreignId('petugas_input_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {

        Schema::dropIfExists('penandatanganans');
        Schema::dropIfExists('pegawai_staff');
    }
};
