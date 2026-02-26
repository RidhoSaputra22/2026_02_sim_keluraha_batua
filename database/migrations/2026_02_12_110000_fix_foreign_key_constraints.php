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

        // Fix wilayah hierarchy
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
    }

    public function down(): void
    {
        // Revert rt_rw_pengurus
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
    }
};
