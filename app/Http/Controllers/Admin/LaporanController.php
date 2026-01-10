<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Laporan;
use App\Models\LaporanAttachment;
use App\Models\Pengumuman;
use App\Support\LaporanCode;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $status = $request->get('status'); // published|draft|null
        $jenis = $request->get('jenis'); // pengumuman|kegiatan|null
        $from = $request->get('from');
        $to = $request->get('to');

        $fromDate = $toDate = null;
        try { $fromDate = $from ? Carbon::parse($from)->startOfDay() : null; } catch (\Exception $e) {}
        try { $toDate = $to ? Carbon::parse($to)->endOfDay() : null; } catch (\Exception $e) {}

        $items = collect();

        if (Schema::hasTable('pengumumen') && ($jenis === null || $jenis === '' || $jenis === 'pengumuman')) {
            $pengumumanItems = Pengumuman::query()
                ->when($q, function($qr) use ($q){
                    $qr->where('judul','like',"%$q%")
                       ->orWhere('kategori','like',"%$q%")
                       ->orWhere('isi','like',"%$q%");
                })
                ->when($status === 'published', fn($qr)=>$qr->where('is_published', true))
                ->when($status === 'draft', fn($qr)=>$qr->where('is_published', false))
                ->latest('published_at')
                ->latest('id')
                ->get()
                ->map(function($p){
                    $date = $p->published_at ?? $p->created_at;
                    return (object)[
                        'type' => 'pengumuman',
                        'type_label' => 'Pengumuman',
                        'title' => $p->judul,
                        'meta_label' => 'Kategori',
                        'meta_value' => $p->kategori ?? '-',
                        'status_label' => $p->is_published ? 'Published' : 'Draft',
                        'status_class' => $p->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700',
                        'date' => $date,
                        'date_label' => $date ? $date->format('d M Y H:i') : '-',
                        'date_meta' => $p->published_at ? 'Terbit' : 'Dibuat',
                        'detail_url' => route('admin.pengumuman.show', $p),
                    ];
                });
            $items = $items->merge($pengumumanItems);
        }

        if (Schema::hasTable('kegiatans') && ($jenis === null || $jenis === '' || $jenis === 'kegiatan')) {
            $kegiatanItems = Kegiatan::query()
                ->when($q, function($qr) use ($q){
                    $qr->where('judul','like',"%$q%")
                       ->orWhere('lokasi','like',"%$q%")
                       ->orWhere('deskripsi','like',"%$q%");
                })
                ->when($status === 'published', fn($qr)=>$qr->where('is_published', true))
                ->when($status === 'draft', fn($qr)=>$qr->where('is_published', false))
                ->latest('waktu_mulai')
                ->latest('id')
                ->get()
                ->map(function($k){
                    $date = $k->waktu_mulai ?? $k->created_at;
                    $end = $k->waktu_selesai;
                    return (object)[
                        'type' => 'kegiatan',
                        'type_label' => 'Kegiatan',
                        'title' => $k->judul,
                        'meta_label' => 'Lokasi',
                        'meta_value' => $k->lokasi ?? '-',
                        'status_label' => $k->is_published ? 'Published' : 'Draft',
                        'status_class' => $k->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-700',
                        'date' => $date,
                        'date_label' => $date ? $date->format('d M Y H:i') : '-',
                        'date_meta' => $end ? 'Selesai '.$end->format('d M Y H:i') : null,
                        'detail_url' => route('admin.kegiatan.show', $k),
                    ];
                });
            $items = $items->merge($kegiatanItems);
        }

        if ($fromDate) {
            $items = $items->filter(fn($item)=>$item->date && $item->date->greaterThanOrEqualTo($fromDate));
        }
        if ($toDate) {
            $items = $items->filter(fn($item)=>$item->date && $item->date->lessThanOrEqualTo($toDate));
        }

        $items = $items->sortByDesc('date')->values();

        $perPage = 15;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $pagedItems = $items->slice(($page - 1) * $perPage, $perPage)->values();
        $items = new LengthAwarePaginator($pagedItems, $items->count(), $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return view('admin.laporan.index', compact('items','q','status','jenis','from','to'));
    }

    public function create()
    {
        abort_unless(Schema::hasTable('laporans'), 404);
        return view('admin.laporan.create');
    }

    public function store(Request $request)
    {
        abort_unless(Schema::hasTable('laporans'), 404);

        $categories = ['Pengaduan','Saran','Fasilitas','Keuangan','Kegiatan','Lainnya'];

        $data = $request->validate([
            'judul' => ['required','string','max:255'],
            'kategori' => ['required','string','max:100', Rule::in($categories)],
            'deskripsi' => ['nullable','string'],
            'files' => ['nullable','array','max:10'],
            'files.*' => ['file','max:8192','mimes:pdf,doc,docx,xls,xlsx,csv,jpg,jpeg,png,webp,zip'],
        ]);

        $laporan = new Laporan();
        $laporan->judul = $data['judul'];
        $laporan->kategori = $data['kategori'];
        $laporan->deskripsi = $data['deskripsi'] ?? null;
        $laporan->kode = LaporanCode::generate();
        $laporan->reporter_id = Auth::id();
        $laporan->status = 'open';
        $laporan->attachments_count = 0;
        $laporan->comments_count = 0;
        $laporan->save();

        if (!empty($data['files'])) {
            foreach ($data['files'] as $file) {
                $path = $file->store('laporan/files','public');
                LaporanAttachment::create([
                    'laporan_id' => $laporan->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_mime' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => Auth::id(),
                ]);
                $laporan->increment('attachments_count');
            }
        }

        return redirect()->route('admin.laporan.show', $laporan)->with('success','Laporan berhasil ditambahkan.');
    }

    public function show(Laporan $laporan)
    {
        $laporan->load(['reporter','attachments','comments.user']);
        return view('admin.laporan.show', compact('laporan'));
    }

    public function attachmentDownload(Laporan $laporan, LaporanAttachment $attachment)
    {
        abort_unless($attachment->laporan_id === $laporan->id, 404);
        abort_unless($attachment->file_path && Storage::disk('public')->exists($attachment->file_path), 404);
        $name = $attachment->file_name ?: ('lampiran-'.$attachment->id);
        return Storage::disk('public')->download($attachment->file_path, $name, [ 'Content-Type' => $attachment->file_mime ?? 'application/octet-stream' ]);
    }
}
