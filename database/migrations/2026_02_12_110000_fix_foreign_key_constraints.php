<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix foreign key constraints that were using restrictOnDelete
     * Change to cascadeOnDelete to prevent constraint errors
     */
    public function up(): void
    {
        // Fix penilaian_rt_rw_details.pengurus_id
        Schema::table('penilaian_rt_rw_details', function (Blueprint $table) {
            $table->dropForeign(['pengurus_id']);
        });
        Schema::table('penilaian_rt_rw_details', function (Blueprint $table) {
            $table->foreign('pengurus_id')
                ->references('id')->on('rt_rw_pengurus')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        // Fix rt_rw_pengurus foreign keys
        Schema::table('rt_rw_pengurus', function (Blueprint $table) {
            $table->dropForeign(['kelurahan_id']);
            $table->dropForeign(['penduduk_id']);
            $table->dropForeign(['jabatan_id']);
        });
        Schema::table('rt_rw_pengurus', function (Blueprint $table) {
            $table->foreign('kelurahan_id')
                ->references('id')->on('kelurahans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('penduduk_id')
                ->references('id')->on('penduduks')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('jabatan_id')
                ->references('id')->on('jabatan_rt_rw')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        // Fix penandatanganans.pegawai_id
        Schema::table('penandatanganans', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
        });
        Schema::table('penandatanganans', function (Blueprint $table) {
            $table->foreign('pegawai_id')
                ->references('id')->on('pegawai_staff')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        // Fix penilaian_periodes.kelurahan_id
        Schema::table('penilaian_periodes', function (Blueprint $table) {
            $table->dropForeign(['kelurahan_id']);
        });
        Schema::table('penilaian_periodes', function (Blueprint $table) {
            $table->foreign('kelurahan_id')
                ->references('id')->on('kelurahans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        // Fix wilayah hierarchy (optional - makes it easier to delete)
        Schema::table('kelurahans', function (Blueprint $table) {
            $table->dropForeign(['kecamatan_id']);
        });
        Schema::table('kelurahans', function (Blueprint $table) {
            $table->foreign('kecamatan_id')
                ->references('id')->on('kecamatans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        Schema::table('rws', function (Blueprint $table) {
            $table->dropForeign(['kelurahan_id']);
        });
        Schema::table('rws', function (Blueprint $table) {
            $table->foreign('kelurahan_id')
                ->references('id')->on('kelurahans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        Schema::table('rts', function (Blueprint $table) {
            $table->dropForeign(['rw_id']);
        });
        Schema::table('rts', function (Blueprint $table) {
            $table->foreign('rw_id')
                ->references('id')->on('rws')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });

        // Fix surats.kelurahan_id
        Schema::table('surats', function (Blueprint $table) {
            $table->dropForeign(['kelurahan_id']);
        });
        Schema::table('surats', function (Blueprint $table) {
            $table->foreign('kelurahan_id')
                ->references('id')->on('kelurahans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // Revert back to restrictOnDelete (not recommended)
        Schema::table('penilaian_rt_rw_details', function (Blueprint $table) {
            $table->dropForeign(['pengurus_id']);
        });
        Schema::table('penilaian_rt_rw_details', function (Blueprint $table) {
            $table->foreign('pengurus_id')
                ->references('id')->on('rt_rw_pengurus')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('rt_rw_pengurus', function (Blueprint $table) {
            $table->dropForeign(['kelurahan_id']);
            $table->dropForeign(['penduduk_id']);
            $table->dropForeign(['jabatan_id']);
        });
        Schema::table('rt_rw_pengurus', function (Blueprint $table) {
            $table->foreign('kelurahan_id')
                ->references('id')->on('kelurahans')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreign('penduduk_id')
                ->references('id')->on('penduduks')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreign('jabatan_id')
                ->references('id')->on('jabatan_rt_rw')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('penandatanganans', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
        });
        Schema::table('penandatanganans', function (Blueprint $table) {
            $table->foreign('pegawai_id')
                ->references('id')->on('pegawai_staff')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('penilaian_periodes', function (Blueprint $table) {
            $table->dropForeign(['kelurahan_id']);
        });
        Schema::table('penilaian_periodes', function (Blueprint $table) {
            $table->foreign('kelurahan_id')
                ->references('id')->on('kelurahans')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('kelurahans', function (Blueprint $table) {
            $table->dropForeign(['kecamatan_id']);
        });
        Schema::table('kelurahans', function (Blueprint $table) {
            $table->foreign('kecamatan_id')
                ->references('id')->on('kecamatans')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('rws', function (Blueprint $table) {
            $table->dropForeign(['kelurahan_id']);
        });
        Schema::table('rws', function (Blueprint $table) {
            $table->foreign('kelurahan_id')
                ->references('id')->on('kelurahans')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('rts', function (Blueprint $table) {
            $table->dropForeign(['rw_id']);
        });
        Schema::table('rts', function (Blueprint $table) {
            $table->foreign('rw_id')
                ->references('id')->on('rws')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        Schema::table('surats', function (Blueprint $table) {
            $table->dropForeign(['kelurahan_id']);
        });
        Schema::table('surats', function (Blueprint $table) {
            $table->foreign('kelurahan_id')
                ->references('id')->on('kelurahans')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }
};
