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
        Schema::table('users', function (Blueprint $table) {
            Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom 'role' bertipe string setelah kolom 'password'.
            // Defaultnya adalah 'pegawai', yang berarti pengguna baru akan terdaftar sebagai pegawai biasa.
            // Anda bisa mengubah ini menjadi 'admin' jika pendaftaran awal adalah untuk admin.
            $table->string('role')->default('pegawai')->after('password');
            // Menambahkan indeks pada kolom 'role' untuk meningkatkan performa query,
            // terutama jika Anda akan sering memfilter pengguna berdasarkan peran.
            $table->index('role');
        });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            Schema::table('users', function (Blueprint $table) {
            // Penting: Hapus indeks sebelum menghapus kolom untuk menghindari error.
            $table->dropIndex(['role']);
            $table->dropColumn('role');
        });
        });
    }
};
