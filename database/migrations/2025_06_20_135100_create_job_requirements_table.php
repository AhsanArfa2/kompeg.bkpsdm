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
        Schema::create('job_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('job_name');
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->onDelete('set null'); // Jika syarat jabatan spesifik per instansi
            $table->text('description')->nullable();
            $table->json('requirements_json')->nullable(); // Untuk detail syarat dalam format JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_requirements');
    }
};
