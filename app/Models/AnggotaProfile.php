<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','nik','nama_lengkap','phone','alamat','tanggal_lahir','jenis_kelamin','pekerjaan','organisasi','avatar_path','is_active','joined_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_lahir' => 'date',
        'joined_at' => 'datetime',
    ];

    public function user(){ return $this->belongsTo(User::class); }
}
