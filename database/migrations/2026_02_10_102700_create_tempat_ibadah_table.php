<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tempat_ibadah', function (Blueprint $table) {
            $table->id();
            $table->string('kelurahan')->nullable();
            $table->string('tempat_ibadah')->nullable(); // jenis
            $table->string('nama');
            $table->text('alamat')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('pengurus')->nullable();
            $table->string('arsip')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['kelurahan']);
            $table->index(['tempat_ibadah']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tempat_ibadah');
    }
};
