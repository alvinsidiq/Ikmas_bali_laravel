<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Arsip extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'judul','slug','kategori','tahun','nomor_dokumen','tags','ringkasan','thumbnail_url',
        'file_path','file_name','file_mime','file_size','is_published','published_at','uploaded_by'
    ];

    protected $casts = [
        'tahun' => 'integer',
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

    public function uploader(){ return $this->belongsTo(User::class, 'uploaded_by'); }

    // Helpers
    public function getTagsArrayAttribute(): array
    {
        return collect(explode(',', (string)$this->tags))
            ->map(fn($s)=>trim($s))
            ->filter()->values()->all();
    }

    // Scopes
    public function scopePublished($q){ return $q->where('is_published', true); }
    public function scopeYear($q, $y){ return $q->where('tahun', $y); }
    public function scopeCategory($q, $c){ return $q->where('kategori', $c); }
    public function scopeWithTag($q, $t){ return $q->where('tags','like',"%$t%"); }
}
