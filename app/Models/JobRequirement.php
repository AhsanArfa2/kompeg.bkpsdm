<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobRequirement extends Model
{
    use HasFactory;

     protected $fillable = [
        'job_name',
        'institution_id',
        'description',
        'requirements_json',
    ];

    protected $casts = [
        'requirements_json' => 'array', // Automatically cast JSON to array
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function analysisResults()
    {
        return $this->hasMany(AnalysisResult::class);
    }
}
