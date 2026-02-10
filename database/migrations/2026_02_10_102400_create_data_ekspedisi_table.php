<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_ekspedisi', function (Blueprint $table) {
            $table->id();
            $table->string('pemilik_usaha')->nullable();
            $table->string('ekspedisi');
            $table->text('alamat')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->string('telp_hp', 30)->nullable();
            $table->string('kegiatan_ekspedisi')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['ekspedisi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_ekspedisi');
    }
};
