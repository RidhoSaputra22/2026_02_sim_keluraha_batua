<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_usaha', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        // Add jenis_usaha_id to umkms table
        Schema::table('umkms', function (Blueprint $table) {
            $table->foreignId('jenis_usaha_id')->nullable()->after('sektor_umkm')->constrained('jenis_usaha')->nullOnDelete();
            $table->string('status')->nullable()->after('jenis_usaha_id');
        });
    }

    public function down(): void
    {
        Schema::table('umkms', function (Blueprint $table) {
            $table->dropForeign(['jenis_usaha_id']);
            $table->dropColumn(['jenis_usaha_id', 'status']);
        });
        Schema::dropIfExists('jenis_usaha');
    }
};
