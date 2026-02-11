<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('survey_layanans', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('survey_kepuasans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelurahan_id')->constrained('kelurahans')->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('jenis_kelamin', ['L','P'])->nullable();
            $table->unsignedSmallInteger('umur')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->foreignId('jenis_layanan_id')->nullable()->constrained('survey_layanans')->nullOnDelete();
            $table->integer('jumlah_nilai')->default(0);
            $table->decimal('nilai_rata_rata', 6, 2)->default(0);
            $table->timestamps();

            $table->index(['kelurahan_id','jenis_layanan_id']);
        });
    }

    public function down(): void
    {

        Schema::dropIfExists('survey_kepuasans');
        Schema::dropIfExists('survey_layanans');
    }
};
