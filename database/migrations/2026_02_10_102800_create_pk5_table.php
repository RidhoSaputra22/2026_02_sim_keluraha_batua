<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pk5', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 32)->nullable();
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('pangkat')->nullable();
            $table->string('status')->nullable();
            $table->string('no_telp', 30)->nullable();
            $table->date('tgl_input')->nullable();
            $table->string('petugas_input')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('nip')->references('nip')->on('pegawai_staff')->nullOnDelete()->cascadeOnUpdate();

            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pk5');
    }
};
