<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DokumentasiMedia extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [ 'album_id','media_path','mime','size','caption','is_cover','sort_order','uploaded_by' ];

    public function album(){ return $this->belongsTo(DokumentasiAlbum::class, 'album_id'); }
    public function uploader(){ return $this->belongsTo(User::class, 'uploaded_by'); }
}
