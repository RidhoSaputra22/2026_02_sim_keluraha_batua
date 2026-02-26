<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('jabatan_rt_rw', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('rt_rw_pengurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelurahan_id')->constrained('kelurahans')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('penduduk_id')->constrained('penduduks')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('jabatan_id')->constrained('jabatan_rt_rw')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('rw_id')->nullable()->constrained('rws')->nullOnDelete();
            $table->foreignId('rt_id')->nullable()->constrained('rts')->nullOnDelete();
            $table->date('tgl_mulai')->nullable();
            $table->string('status')->nullable();
            $table->string('alamat')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('no_npwp')->nullable();
            $table->timestamps();

            $table->index(['kelurahan_id','rw_id','rt_id']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('rt_rw_pengurus');
        Schema::dropIfExists('jabatan_rt_rw');
    }
};
