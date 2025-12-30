<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DokumentasiAlbum extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'judul','slug','tanggal_kegiatan','lokasi','deskripsi','tags','cover_path','media_count','view_count','is_published','published_at','created_by'
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function($m){ if (empty($m->slug)) $m->slug = static::uniqueSlug($m->judul); });
        static::updating(function($m){ if ($m->isDirty('judul')) $m->slug = static::uniqueSlug($m->judul, $m->id); });
    }

    public static function uniqueSlug(string $judul, ?int $ignoreId=null): string
    {
        $base = Str::slug($judul); $slug = $base; $i=1;
        while (static::where('slug',$slug)->when($ignoreId, fn($q)=>$q->where('id','!=',$ignoreId))->exists()) $slug = $base.'-'.$i++;
        return $slug;
    }

    public function medias(){ return $this->hasMany(DokumentasiMedia::class, 'album_id')->orderBy('sort_order'); }
    public function creator(){ return $this->belongsTo(User::class, 'created_by'); }

    // Helpers
    public function getTagsArrayAttribute(): array
    { return collect(explode(',', (string)$this->tags))->map(fn($s)=>trim($s))->filter()->values()->all(); }

    // Scopes
    public function scopePublished($q){ return $q->where('is_published', true); }
}
