<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('kecamatans', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('kelurahans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained('kecamatans')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('nama');
            $table->unique(['kecamatan_id','nama']);
            $table->timestamps();
        });

        Schema::create('rws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelurahan_id')->constrained('kelurahans')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedSmallInteger('nomor');
            $table->unique(['kelurahan_id','nomor']);
            $table->timestamps();
        });

        Schema::create('rts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rw_id')->constrained('rws')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedSmallInteger('nomor');
            $table->unique(['rw_id','nomor']);
            $table->timestamps();
        });
    }

    public function down(): void
    {

        Schema::dropIfExists('rts');
        Schema::dropIfExists('rws');
        Schema::dropIfExists('kelurahans');
        Schema::dropIfExists('kecamatans');
    }
};
