<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryDeletedEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_name',
        'nip',
        'last_institution',
        'last_jabatan',
        'golongan',
        'deleted_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
