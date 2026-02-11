<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->foreignId('verifikator_id')->nullable()->after('petugas_input_id')->constrained('users')->nullOnDelete();
            $table->foreignId('penandatangan_pejabat_id')->nullable()->after('verifikator_id')->constrained('penandatanganans')->nullOnDelete();
            $table->dateTime('tgl_verifikasi')->nullable()->after('penandatangan_pejabat_id');
            $table->dateTime('tgl_ttd')->nullable()->after('tgl_verifikasi');
            $table->text('catatan_verifikasi')->nullable()->after('tgl_ttd');
            $table->text('catatan_penandatangan')->nullable()->after('catatan_verifikasi');
        });
    }

    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropForeign(['verifikator_id']);
            $table->dropForeign(['penandatangan_pejabat_id']);
            $table->dropColumn([
                'verifikator_id',
                'penandatangan_pejabat_id',
                'tgl_verifikasi',
                'tgl_ttd',
                'catatan_verifikasi',
                'catatan_penandatangan',
            ]);
        });
    }
};
