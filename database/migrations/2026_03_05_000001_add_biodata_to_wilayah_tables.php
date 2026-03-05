<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Kelurahan biodata ────────────────────────────────
        Schema::table('kelurahans', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('nama');
            $table->string('kode_pos', 10)->nullable()->after('foto');
            $table->decimal('luas_area', 10, 2)->nullable()->comment('Luas dalam km²')->after('kode_pos');
            $table->string('alamat_kantor')->nullable()->after('luas_area');
            $table->string('no_telp', 20)->nullable()->after('alamat_kantor');
            $table->string('email')->nullable()->after('no_telp');
            $table->string('website')->nullable()->after('email');
            $table->string('nama_lurah')->nullable()->after('website');
            $table->string('nip_lurah', 30)->nullable()->after('nama_lurah');
            $table->text('visi')->nullable()->after('nip_lurah');
            $table->text('misi')->nullable()->after('visi');
            $table->text('deskripsi')->nullable()->after('misi');
            $table->string('batas_utara')->nullable()->after('deskripsi');
            $table->string('batas_selatan')->nullable()->after('batas_utara');
            $table->string('batas_timur')->nullable()->after('batas_selatan');
            $table->string('batas_barat')->nullable()->after('batas_timur');
        });

        // ─── RW biodata ──────────────────────────────────────
        Schema::table('rws', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('warna');
            $table->decimal('luas_area', 10, 2)->nullable()->comment('Luas dalam km²')->after('foto');
            $table->string('alamat_sekretariat')->nullable()->after('luas_area');
            $table->string('no_telp', 20)->nullable()->after('alamat_sekretariat');
            $table->text('deskripsi')->nullable()->after('no_telp');
            $table->text('fasilitas')->nullable()->comment('Fasilitas umum di RW')->after('deskripsi');
            $table->string('batas_utara')->nullable()->after('fasilitas');
            $table->string('batas_selatan')->nullable()->after('batas_utara');
            $table->string('batas_timur')->nullable()->after('batas_selatan');
            $table->string('batas_barat')->nullable()->after('batas_timur');
        });

        // ─── RT biodata ──────────────────────────────────────
        Schema::table('rts', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('nomor');
            $table->decimal('luas_area', 10, 2)->nullable()->comment('Luas dalam km²')->after('foto');
            $table->string('alamat_pos')->nullable()->after('luas_area');
            $table->string('no_telp', 20)->nullable()->after('alamat_pos');
            $table->text('deskripsi')->nullable()->after('no_telp');
            $table->text('fasilitas')->nullable()->comment('Fasilitas umum di RT')->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('kelurahans', function (Blueprint $table) {
            $table->dropColumn([
                'foto', 'kode_pos', 'luas_area', 'alamat_kantor', 'no_telp', 'email',
                'website', 'nama_lurah', 'nip_lurah', 'visi', 'misi', 'deskripsi',
                'batas_utara', 'batas_selatan', 'batas_timur', 'batas_barat',
            ]);
        });

        Schema::table('rws', function (Blueprint $table) {
            $table->dropColumn([
                'foto', 'luas_area', 'alamat_sekretariat', 'no_telp', 'deskripsi',
                'fasilitas', 'batas_utara', 'batas_selatan', 'batas_timur', 'batas_barat',
            ]);
        });

        Schema::table('rts', function (Blueprint $table) {
            $table->dropColumn([
                'foto', 'luas_area', 'alamat_pos', 'no_telp', 'deskripsi', 'fasilitas',
            ]);
        });
    }
};
