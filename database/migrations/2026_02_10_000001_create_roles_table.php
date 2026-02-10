<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();        // admin, operator, verifikator, penandatangan, rt_rw, warga
            $table->string('label');                  // Admin Sistem, Operator Kelurahan, etc.
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();  // JSON array of permissions
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add role_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
            $table->string('phone')->nullable()->after('email');
            $table->string('nip')->nullable()->after('phone');       // NIP for staff
            $table->string('nik')->nullable()->after('nip');         // NIK for warga
            $table->string('jabatan')->nullable()->after('nik');
            $table->string('wilayah_rt')->nullable()->after('jabatan');  // For RT/RW role
            $table->string('wilayah_rw')->nullable()->after('wilayah_rt');
            $table->boolean('is_active')->default(true)->after('remember_token');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn([
                'role_id', 'phone', 'nip', 'nik', 'jabatan',
                'wilayah_rt', 'wilayah_rw', 'is_active', 'last_login_at',
            ]);
        });

        Schema::dropIfExists('roles');
    }
};
