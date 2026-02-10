<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_faskes', function (Blueprint $table) {
            $table->id();
            $table->string('nama_rs');
            $table->text('alamat')->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('jenis')->nullable();
            $table->string('kelas')->nullable();
            $table->string('jenis_pelayanan')->nullable();
            $table->string('akreditasi')->nullable();
            $table->string('telp', 30)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['jenis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_faskes');
    }
};
