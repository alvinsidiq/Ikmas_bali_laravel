<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama','tahun_ajaran','mulai','selesai','is_active',
    ];

    protected $casts = [
        'mulai' => 'date',
        'selesai' => 'date',
        'is_active' => 'boolean',
    ];
}
