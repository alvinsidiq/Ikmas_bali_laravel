<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\LaporanAttachment;
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
        $st = $request->get('status'); // open|in_progress|resolved|rejected
        $reporter = $request->get('reporter');
        $jenis = $request->get('jenis');
        $from = $request->get('from');
        $to = $request->get('to');

        $categoryMap = [
            'pengaduan' => ['Pengaduan'],
            'saran' => ['Saran'],
            'fasilitas' => ['Fasilitas'],
            'keuangan' => ['Keuangan'],
            'kegiatan' => ['Kegiatan'],
            'lainnya' => ['Lainnya'],
        ];

        $fromDate = $toDate = null;
        try { $fromDate = $from ? Carbon::parse($from)->startOfDay() : null; } catch (\Exception $e) {}
        try { $toDate = $to ? Carbon::parse($to)->endOfDay() : null; } catch (\Exception $e) {}

        if (!Schema::hasTable('laporans')) {
            $items = new LengthAwarePaginator([], 0, 15, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
            return view('admin.laporan.index', compact('items','q','st','reporter','jenis','from','to'));
        }

        $items = Laporan::query()
            ->with('reporter')
            ->when($q, function($qr) use ($q){
                $qr->where(function($x) use ($q){
                    $x->where('judul','like',"%$q%")
                      ->orWhere('deskripsi','like',"%$q%")
                      ->orWhere('kode','like',"%$q%");
                });
            })
            ->when($jenis, function($qr) use ($jenis, $categoryMap){
                $values = $categoryMap[$jenis] ?? [$jenis];
                $qr->whereIn('kategori', (array)$values);
            })
            ->when($st, fn($qr)=>$qr->where('status',$st))
            ->when($reporter, function($qr) use ($reporter){
                $qr->whereHas('reporter', function($u) use ($reporter){
                    $u->where('name','like',"%$reporter%")
                      ->orWhere('email','like',"%$reporter%");
                });
            })
            ->when($fromDate, fn($qr)=>$qr->where('created_at','>=',$fromDate))
            ->when($toDate, fn($qr)=>$qr->where('created_at','<=',$toDate))
            ->latest('status')
            ->latest('updated_at')
            ->latest('id')
            ->paginate(15)->withQueryString();

        return view('admin.laporan.index', compact('items','q','st','reporter','jenis','from','to'));
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
