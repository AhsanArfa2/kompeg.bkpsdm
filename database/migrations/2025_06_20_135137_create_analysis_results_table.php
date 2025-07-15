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
        Schema::create('analysis_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('job_requirement_id')->nullable()->constrained('job_requirements')->onDelete('set null');
            $table->date('analysis_date');
            $table->string('result_status'); // e.g., 'Siap Naik Jabatan', 'Perlu Pelatihan'
            $table->decimal('analysis_percentage', 5, 2)->default(0.00); // Persentase hasil analisis
            $table->json('details_json')->nullable(); // Detail hasil analisis dalam format JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analysis_results');
    }
};
