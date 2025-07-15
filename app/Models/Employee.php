<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Atribut yang dapat diisi secara massal untuk tabel 'employees'.
     * Pastikan 'nip' dan 'golongan' ada di sini.
     */
    protected $fillable = [
        'user_id',
        'institution_id',
        'name',
        'nip', // Ditambahkan untuk pendaftaran
        'golongan', // Ditambahkan untuk pendaftaran
        'jabatan',
        'email',
        'phone_number',
        'profile_picture_path',
        'analysis_percentage',
    ];

    /**
     * Menentukan kolom `deleted_at` untuk fitur soft delete.
     */
    protected $dates = ['deleted_at'];

    /**
     * Mendefinisikan relasi many-to-one dengan Model User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan relasi many-to-one dengan Model Institution.
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Mendefinisikan relasi one-to-many dengan EmployeeDocument.
     */
    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    /**
     * Mendefinisikan relasi one-to-many dengan AnalysisResult.
     */
    public function analysisResults()
    {
        return $this->hasMany(AnalysisResult::class);
    }
}
