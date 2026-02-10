<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('npsn', 20)->unique();
            $table->string('nama_sekolah');
            $table->string('tahun_ajar')->nullable();
            $table->unsignedInteger('jumlah_siswa')->default(0);
            $table->unsignedInteger('rombel')->default(0);
            $table->unsignedInteger('jumlah_guru')->default(0);
            $table->unsignedInteger('jumlah_pegawai')->default(0);
            $table->unsignedInteger('ruang_kelas')->default(0);
            $table->unsignedInteger('jumlah_r_lab')->default(0);
            $table->unsignedInteger('jumlah_r_perpus')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['nama_sekolah']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_sekolah');
    }
};
