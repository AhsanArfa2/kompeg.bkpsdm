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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Jika pegawai punya akun user
            $table->foreignId('institution_id')->constrained('institutions')->onDelete('cascade');
            $table->string('name');
            $table->string('nip')->unique();
            $table->string('golongan');
            $table->string('jabatan');
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('profile_picture_path')->nullable();
            $table->float('analysis_percentage')->default(0.0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
