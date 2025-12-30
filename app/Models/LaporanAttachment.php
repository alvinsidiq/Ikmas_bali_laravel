<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaporanAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['laporan_id','file_path','file_name','file_mime','file_size','uploaded_by'];

    public function laporan(){ return $this->belongsTo(Laporan::class); }
    public function uploader(){ return $this->belongsTo(User::class, 'uploaded_by'); }
}

