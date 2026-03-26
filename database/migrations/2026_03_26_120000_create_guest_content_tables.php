<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beritas', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('kategori', 50)->default('berita');
            $table->text('ringkasan')->nullable();
            $table->longText('isi');
            $table->string('gambar')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['kategori', 'is_published']);
            $table->index('published_at');
        });

        Schema::create('dokumen_publiks', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('kategori', 50)->default('transparansi');
            $table->text('deskripsi')->nullable();
            $table->string('file_path');
            $table->string('cover_image')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['kategori', 'is_published']);
            $table->index('published_at');
        });

        Schema::create('layanan_surats', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('estimasi_layanan')->nullable();
            $table->string('biaya')->nullable();
            $table->text('catatan')->nullable();
            $table->string('kontak_petugas')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('layanan_surat_persyaratans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layanan_surat_id')->constrained('layanan_surats')->cascadeOnDelete();
            $table->text('nama');
            $table->string('keterangan')->nullable();
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['layanan_surat_id', 'sort_order']);
        });

        Schema::create('destinasi_wisatas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->string('kategori', 50)->default('wisata');
            $table->text('ringkasan')->nullable();
            $table->longText('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->string('alamat')->nullable();
            $table->string('jam_operasional')->nullable();
            $table->string('harga_tiket')->nullable();
            $table->string('kontak')->nullable();
            $table->string('maps_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->nullable();
            $table->timestamps();

            $table->index(['kategori', 'is_published']);
            $table->index('sort_order');
        });

        Schema::create('pengaduan_wargas', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengaduan')->unique();
            $table->string('nama');
            $table->string('email')->nullable();
            $table->string('no_hp', 30);
            $table->string('kategori', 50);
            $table->string('subjek');
            $table->string('lokasi')->nullable();
            $table->longText('isi_laporan');
            $table->string('lampiran')->nullable();
            $table->string('status', 30)->default('baru');
            $table->text('catatan_admin')->nullable();
            $table->timestamp('ditindaklanjuti_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'kategori']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduan_wargas');
        Schema::dropIfExists('destinasi_wisatas');
        Schema::dropIfExists('layanan_surat_persyaratans');
        Schema::dropIfExists('layanan_surats');
        Schema::dropIfExists('dokumen_publiks');
        Schema::dropIfExists('beritas');
    }
};
