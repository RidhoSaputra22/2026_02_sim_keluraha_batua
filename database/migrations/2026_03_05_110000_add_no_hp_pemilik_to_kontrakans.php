<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kontrakans', function (Blueprint $table) {
            $table->string('no_hp_pemilik', 20)->nullable()->after('pemilik');
        });
    }

    public function down(): void
    {
        Schema::table('kontrakans', function (Blueprint $table) {
            $table->dropColumn('no_hp_pemilik');
        });
    }
};
