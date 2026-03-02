<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retribusi_sampahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelurahan_id')->nullable()->constrained('kelurahans')->nullOnDelete();
            $table->foreignId('rw_id')->nullable()->constrained('rws')->nullOnDelete();
            $table->foreignId('rt_id')->nullable()->constrained('rts')->nullOnDelete();
            $table->string('nama_nasabah');
            $table->string('alamat')->nullable();
            $table->string('no_skrd')->nullable();               // Nomor SKRD
            $table->decimal('beban', 15, 2)->nullable()->default(0);  // Beban dalam Rp
            $table->enum('status', ['Lunas', 'Belum'])->default('Belum');
            $table->string('tahun')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retribusi_sampahs');
    }
};
