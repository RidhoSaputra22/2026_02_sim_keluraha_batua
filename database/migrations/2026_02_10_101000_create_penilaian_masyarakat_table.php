<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_masyarakat', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_kelamin', ['L','P'])->nullable();
            $table->unsignedInteger('umur')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('jenis_layanan')->nullable();
            $table->unsignedInteger('jumlah_nilai')->default(0);
            $table->decimal('nilai_rata_rata', 5, 2)->default(0);
            $table->string('wilayah_penugasan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['jenis_layanan']);
            $table->index(['wilayah_penugasan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_masyarakat');
    }
};
