<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('kelurahan')->nullable();
            $table->string('nama_sekolah');
            $table->string('jenjang')->nullable();
            $table->string('status')->nullable();
            $table->text('alamat')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('arsip')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['kelurahan']);
            $table->index(['jenjang']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_sekolah');
    }
};
