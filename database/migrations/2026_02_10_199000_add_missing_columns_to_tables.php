<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── data_penduduk: add missing columns ──────────────────────────
        Schema::table('data_penduduk', function (Blueprint $table) {
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('kewarganegaraan', 5)->nullable();
            $table->text('keterangan')->nullable();
        });

        // ── data_keluarga: add missing columns ─────────────────────────
        Schema::table('data_keluarga', function (Blueprint $table) {
            $table->string('nik_kepala_keluarga', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('status', 20)->nullable();
        });

        // ── pegawai_staff: rename gol→golongan, add missing columns ────
        Schema::table('pegawai_staff', function (Blueprint $table) {
            $table->renameColumn('gol', 'golongan');
        });
        Schema::table('pegawai_staff', function (Blueprint $table) {
            $table->string('nik', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telp', 30)->nullable();
            $table->string('email')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->date('tgl_mulai')->nullable();
        });

        // ── penandatanganan: add missing columns ───────────────────────
        Schema::table('penandatanganan', function (Blueprint $table) {
            $table->string('nik', 20)->nullable();
            $table->string('golongan', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('alamat')->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->text('ttd_digital')->nullable();
        });

        // ── daftar_surat_keluar: add nik & penandatangan_id ────────────
        Schema::table('daftar_surat_keluar', function (Blueprint $table) {
            $table->string('nik', 20)->nullable();
            $table->foreignId('penandatangan_id')->nullable();
        });

        // ── data_rt_rw: add missing columns ────────────────────────────
        Schema::table('data_rt_rw', function (Blueprint $table) {
            $table->string('kecamatan')->nullable();
            $table->string('email')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->string('periode', 20)->nullable();
            $table->string('foto')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('petugas_input')->nullable();
            $table->date('tgl_input')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('data_penduduk', function (Blueprint $table) {
            $table->dropColumn(['tempat_lahir', 'tanggal_lahir', 'pekerjaan', 'kewarganegaraan', 'keterangan']);
        });

        Schema::table('data_keluarga', function (Blueprint $table) {
            $table->dropColumn(['nik_kepala_keluarga', 'alamat', 'kecamatan', 'kelurahan', 'status']);
        });

        Schema::table('pegawai_staff', function (Blueprint $table) {
            $table->dropColumn(['nik', 'alamat', 'no_telp', 'email', 'tgl_lahir', 'tgl_mulai']);
        });
        Schema::table('pegawai_staff', function (Blueprint $table) {
            $table->renameColumn('golongan', 'gol');
        });

        Schema::table('penandatanganan', function (Blueprint $table) {
            $table->dropColumn(['nik', 'golongan', 'email', 'alamat', 'tgl_mulai', 'tgl_selesai', 'ttd_digital']);
        });

        Schema::table('daftar_surat_keluar', function (Blueprint $table) {
            $table->dropColumn(['nik', 'penandatangan_id']);
        });

        Schema::table('data_rt_rw', function (Blueprint $table) {
            $table->dropColumn(['kecamatan', 'email', 'tgl_selesai', 'periode', 'foto', 'keterangan', 'petugas_input', 'tgl_input']);
        });
    }
};
