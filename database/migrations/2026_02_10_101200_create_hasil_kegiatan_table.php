<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('hari_tanggal')->nullable();
            $table->string('agenda')->nullable();
            $table->longText('notulen')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('arsip')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_kegiatan');
    }
};
