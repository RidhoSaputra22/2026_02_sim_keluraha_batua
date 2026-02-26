<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('umkms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelurahan_id')->nullable()->constrained('kelurahans')->nullOnDelete();
            $table->foreignId('rt_id')->nullable()->constrained('rts')->nullOnDelete();
            $table->foreignId('penduduk_id')->nullable()->constrained('penduduks')->nullOnDelete();
            $table->string('nama_pemilik')->nullable();
            $table->string('nik_pemilik')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('nama_ukm');
            $table->string('alamat')->nullable();
            $table->string('sektor_umkm')->nullable();
            $table->timestamps();
        });

        Schema::create('sekolahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelurahan_id')->nullable()->constrained('kelurahans')->nullOnDelete();
            $table->string('npsn')->nullable()->unique();
            $table->string('nama_sekolah');
            $table->string('jenjang')->nullable();
            $table->string('status')->nullable();
            $table->string('alamat')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('tahun_ajar')->nullable();
            $table->unsignedInteger('jumlah_siswa')->nullable();
            $table->unsignedInteger('rombel')->nullable();
            $table->unsignedInteger('jumlah_guru')->nullable();
            $table->unsignedInteger('jumlah_pegawai')->nullable();
            $table->unsignedInteger('ruang_kelas')->nullable();
            $table->unsignedInteger('jumlah_r_lab')->nullable();
            $table->unsignedInteger('jumlah_r_perpus')->nullable();
            $table->timestamps();
        });

        Schema::create('faskes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelurahan_id')->nullable()->constrained('kelurahans')->nullOnDelete();
            $table->string('nama_rs');
            $table->string('alamat')->nullable();
            $table->foreignId('rw_id')->nullable()->constrained('rws')->nullOnDelete();
            $table->string('jenis')->nullable();
            $table->string('kelas')->nullable();
            $table->string('jenis_pelayanan')->nullable();
            $table->string('akreditasi')->nullable();
            $table->string('telp')->nullable();
            $table->timestamps();
        });

        Schema::create('petugas_kebersihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelurahan_id')->nullable()->constrained('kelurahans')->nullOnDelete();
            $table->foreignId('penduduk_id')->nullable()->constrained('penduduks')->nullOnDelete();
            $table->string('nama')->nullable();
            $table->string('nik')->nullable();
            $table->string('unit_kerja')->nullable();
            $table->enum('jenis_kelamin', ['L','P'])->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('tempat_ibadahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelurahan_id')->nullable()->constrained('kelurahans')->nullOnDelete();
            $table->string('tempat_ibadah');
            $table->string('nama')->nullable();
            $table->string('alamat')->nullable();
            $table->foreignId('rt_id')->nullable()->constrained('rts')->nullOnDelete();
            $table->foreignId('rw_id')->nullable()->constrained('rws')->nullOnDelete();
            $table->string('pengurus')->nullable();
            $table->string('arsip_path')->nullable();
            $table->timestamps();
        });

        Schema::create('kendaraans', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_barang')->nullable();
            $table->string('nama_pengemudi')->nullable();
            $table->string('no_polisi')->nullable();
            $table->string('no_rangka')->nullable();
            $table->string('no_mesin')->nullable();
            $table->string('tahun_perolehan')->nullable();
            $table->string('merek_type')->nullable();
            $table->foreignId('kelurahan_id')->nullable()->constrained('kelurahans')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {

        Schema::dropIfExists('kendaraans');
        Schema::dropIfExists('tempat_ibadahs');
        Schema::dropIfExists('petugas_kebersihans');
        Schema::dropIfExists('faskes');
        Schema::dropIfExists('sekolahs');
        Schema::dropIfExists('umkms');
    }
};
