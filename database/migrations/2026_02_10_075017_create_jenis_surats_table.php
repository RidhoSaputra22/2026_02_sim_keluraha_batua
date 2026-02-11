<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jenis_surat', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique()->comment('Kode jenis surat, misal: SKTM, SKD, SKU');
            $table->string('nama')->comment('Nama lengkap jenis surat');
            $table->text('keterangan')->nullable();
            $table->string('format_nomor')->nullable()->comment('Format penomoran, misal: {NO}/SKTM/KB/{BULAN}/{TAHUN}');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_surat');
    }
};
