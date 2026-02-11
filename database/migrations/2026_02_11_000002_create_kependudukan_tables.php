<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keluargas', function (Blueprint $table) {
            $table->id();
            $table->string('no_kk')->unique();

            // NOTE: jangan constrained dulu karena penduduks belum ada
            $table->unsignedBigInteger('kepala_keluarga_id')->nullable();

            $table->unsignedSmallInteger('jumlah_anggota_keluarga')->default(0);
            $table->foreignId('rt_id')->nullable()->constrained('rts')->nullOnDelete();
            $table->dateTime('tgl_input')->nullable();
            $table->foreignId('petugas_input_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('arsip_path')->nullable();
            $table->timestamps();
        });

        Schema::create('penduduks', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 32)->unique();
            $table->string('nama');
            $table->string('alamat')->nullable();

            // Ini aman karena keluargas sudah ada
            $table->foreignId('keluarga_id')->nullable()->constrained('keluargas')->nullOnDelete();
            $table->foreignId('rt_id')->nullable()->constrained('rts')->nullOnDelete();

            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('gol_darah', 3)->nullable();
            $table->string('agama')->nullable();
            $table->string('status_kawin')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('status_data')->nullable();

            $table->dateTime('tgl_input')->nullable();
            $table->foreignId('petugas_input_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Baru tambahkan FK setelah penduduks ada
        Schema::table('keluargas', function (Blueprint $table) {
            $table->foreign('kepala_keluarga_id')
                ->references('id')->on('penduduks')
                ->nullOnDelete();
        });

        Schema::create('penduduk_asings', function (Blueprint $table) {
            $table->id();
            $table->string('no_paspor')->unique();
            $table->string('nama');
            $table->string('alamat')->nullable();
            $table->foreignId('rt_id')->nullable()->constrained('rts')->nullOnDelete();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->dateTime('tgl_input')->nullable();
            $table->foreignId('petugas_input_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('arsip_path')->nullable();
            $table->timestamps();
        });

        Schema::create('ktp_tercetaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penduduk_id')->constrained('penduduks')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('tgl_buat');
            $table->foreignId('petugas_input_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ktp_tercetaks');
        Schema::dropIfExists('penduduk_asings');

        // drop FK dulu sebelum drop tabel
        Schema::table('keluargas', function (Blueprint $table) {
            $table->dropForeign(['kepala_keluarga_id']);
        });

        Schema::dropIfExists('penduduks');
        Schema::dropIfExists('keluargas');
    }
};
