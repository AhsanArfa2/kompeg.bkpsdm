<?php

namespace App\Models;

use illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalysisResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'job_requirement_id',
        'analysis_date',
        'result_status',
        'analysis_percentage', 
        'details_json'
    ];

    protected $casts = [
        'details_json' => 'array', 
        'analysis_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function jobRequirement()
    {
        return $this->belongsTo(JobRequirement::class);
    }
}
