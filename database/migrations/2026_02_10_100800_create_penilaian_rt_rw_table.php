<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_rt_rw', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 20)->nullable();
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->decimal('nilai', 8, 2)->default(0);
            $table->decimal('standar_nilai', 8, 2)->default(0);
            $table->decimal('usulan_nilai_insentif', 12, 2)->default(0);
            $table->date('periode_penilaian')->nullable();
            $table->string('lpj')->nullable();
            $table->string('arsip')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('nik')->references('nik')->on('data_penduduk')->nullOnDelete()->cascadeOnUpdate();

            $table->index(['periode_penilaian']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_rt_rw');
    }
};
