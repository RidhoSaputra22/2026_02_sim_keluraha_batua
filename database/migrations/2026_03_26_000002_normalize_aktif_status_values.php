<?php

use App\Enums\StatusAktifEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('rt_rw_pengurus')
            ->whereRaw('LOWER(status) = ?', [StatusAktifEnum::AKTIF->value])
            ->update(['status' => StatusAktifEnum::AKTIF->value]);

        DB::table('rt_rw_pengurus')
            ->whereRaw('LOWER(status) = ?', [StatusAktifEnum::NONAKTIF->value])
            ->update(['status' => StatusAktifEnum::NONAKTIF->value]);

        DB::table('pegawai_staff')
            ->whereRaw('LOWER(status_pegawai) = ?', [StatusAktifEnum::AKTIF->value])
            ->update(['status_pegawai' => StatusAktifEnum::AKTIF->value]);

        DB::table('pegawai_staff')
            ->whereRaw('LOWER(status_pegawai) = ?', [StatusAktifEnum::NONAKTIF->value])
            ->update(['status_pegawai' => StatusAktifEnum::NONAKTIF->value]);
    }

    public function down(): void
    {
        // Status dinormalisasi ke format baku lowercase, tidak perlu dibalik.
    }
};
