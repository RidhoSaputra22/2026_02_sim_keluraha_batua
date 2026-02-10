<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekap_bulanan_penduduk', function (Blueprint $table) {
            $table->id();
            $table->string('kelurahan')->nullable();
            $table->date('periode'); // gunakan tanggal 1 setiap bulan sebagai penanda periode
            $table->unsignedInteger('data_laki_laki')->default(0);
            $table->unsignedInteger('data_perempuan')->default(0);
            $table->unsignedInteger('data_laki_laki_wna')->default(0);
            $table->unsignedInteger('data_perempuan_wna')->default(0);

            $table->unsignedInteger('lahir_laki_laki')->default(0);
            $table->unsignedInteger('lahir_perempuan')->default(0);
            $table->unsignedInteger('lahir_laki_laki_wna')->default(0);
            $table->unsignedInteger('lahir_perempuan_wna')->default(0);

            $table->unsignedInteger('kematian_laki_laki')->default(0);
            $table->unsignedInteger('kematian_perempuan')->default(0);
            $table->unsignedInteger('kematian_laki_laki_wna')->default(0);
            $table->unsignedInteger('kematian_perempuan_wna')->default(0);

            $table->unsignedInteger('datang_laki_laki')->default(0);
            $table->unsignedInteger('datang_perempuan')->default(0);
            $table->unsignedInteger('datang_laki_laki_wna')->default(0);
            $table->unsignedInteger('datang_perempuan_wna')->default(0);

            $table->unsignedInteger('pindah_laki_laki')->default(0);
            $table->unsignedInteger('pindah_perempuan')->default(0);
            $table->unsignedInteger('pindah_laki_laki_wna')->default(0);
            $table->unsignedInteger('pindah_perempuan_wna')->default(0);

            $table->unsignedInteger('pend_laki_laki')->default(0);
            $table->unsignedInteger('pend_perempuan')->default(0);
            $table->unsignedInteger('pend_laki_laki_wna')->default(0);
            $table->unsignedInteger('pend_perempuan_wna')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['kelurahan','periode']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekap_bulanan_penduduk');
    }
};
