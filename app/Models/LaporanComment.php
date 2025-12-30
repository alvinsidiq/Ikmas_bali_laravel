<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaporanComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['laporan_id','user_id','body','is_internal'];

    public function laporan(){ return $this->belongsTo(Laporan::class); }
    public function user(){ return $this->belongsTo(User::class); }
}

