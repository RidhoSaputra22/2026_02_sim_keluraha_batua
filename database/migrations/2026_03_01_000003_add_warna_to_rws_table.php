<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Default colors per RW nomor for initial migration.
     */
    private array $defaultColors = [
        1  => '#6366f1',
        2  => '#8b5cf6',
        3  => '#ec4899',
        4  => '#f43f5e',
        5  => '#f97316',
        6  => '#eab308',
        7  => '#22c55e',
        8  => '#14b8a6',
        9  => '#06b6d4',
        10 => '#3b82f6',
        11 => '#a855f7',
        12 => '#d946ef',
        13 => '#78716c',
    ];

    public function up(): void
    {
        Schema::table('rws', function (Blueprint $table) {
            $table->string('warna', 7)->default('#6b7280')->after('nomor');
        });

        // Populate based on nomor
        foreach ($this->defaultColors as $nomor => $color) {
            DB::table('rws')->where('nomor', $nomor)->update(['warna' => $color]);
        }
    }

    public function down(): void
    {
        Schema::table('rws', function (Blueprint $table) {
            $table->dropColumn('warna');
        });
    }
};
