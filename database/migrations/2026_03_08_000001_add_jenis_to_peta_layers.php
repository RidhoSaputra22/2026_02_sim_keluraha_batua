<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peta_layers', function (Blueprint $table) {
            $table->string('jenis', 20)->default('polygon')->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('peta_layers', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
    }
};
