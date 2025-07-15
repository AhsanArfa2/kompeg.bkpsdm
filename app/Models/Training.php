<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'details',
        'target_jabatan',
        'training_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'target_jabatan' => 'array', // Jika ini disimpan sebagai JSON
        'training_date' => 'date',   // Mengubah string tanggal menjadi objek Carbon
    ];
}
