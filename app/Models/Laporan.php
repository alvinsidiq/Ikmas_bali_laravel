<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Laporan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kode','reporter_id','judul','kategori','deskripsi','status','resolved_at','rejected_at','rejected_reason','attachments_count','comments_count'
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function reporter(){ return $this->belongsTo(User::class, 'reporter_id'); }
    public function attachments(){ return $this->hasMany(LaporanAttachment::class); }
    public function comments(){ return $this->hasMany(LaporanComment::class)->where('is_internal', false)->latest(); }
}
