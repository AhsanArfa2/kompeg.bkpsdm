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
        Schema::create('history_deleted_employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_name');
            $table->string('nip');
            $table->string('last_institution')->nullable();
            $table->string('last_jabatan')->nullable();
            $table->string('golongan')->nullable();
            $table->timestamp('deleted_at')->nullable(); // Waktu penghapusan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_deleted_employees');
    }
};
