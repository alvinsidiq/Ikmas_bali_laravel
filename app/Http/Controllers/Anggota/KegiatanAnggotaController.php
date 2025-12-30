<?php
namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class KegiatanAnggotaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $w = $request->get('w'); // upcoming|past|null

        $items = Kegiatan::query()
            ->published()
            ->when($q, function($qr) use ($q){
                $qr->where('judul','like',"%$q%")
                   ->orWhere('lokasi','like',"%$q%")
                   ->orWhere('deskripsi','like',"%$q%");
            })
            ->when($w === 'upcoming', fn($qr)=>$qr->where('waktu_mulai','>=', now()))
            ->when($w === 'past', fn($qr)=>$qr->where('waktu_mulai','<', now()))
            ->when(Schema::hasTable('kegiatan_user'), fn($qr)=>$qr->withCount('participants'))
            ->orderBy('waktu_mulai')
            ->paginate(12)
            ->withQueryString();

        $mine = Schema::hasTable('kegiatan_user')
            ? Auth::user()->kegiatanDiikuti()->pluck('kegiatans.id')->toArray()
            : [];

        return view('anggota.kegiatan.index', compact('items','q','w','mine'));
    }

    public function mine(Request $request)
    {
        if (!Schema::hasTable('kegiatan_user')) {
            $items = Kegiatan::query()->whereRaw('1=0')->paginate(12);
            return view('anggota.kegiatan.mine', compact('items'));
        }
        $user = Auth::user();
        $items = $user->kegiatanDiikuti()
            ->wherePivot('status','registered')
            ->with('creator')
            ->orderBy('waktu_mulai')
            ->paginate(12);
        return view('anggota.kegiatan.mine', compact('items'));
    }

    public function show(Kegiatan $kegiatan)
    {
        abort_unless($kegiatan->is_published, 404);
        $user = Auth::user();
        $pivot = Schema::hasTable('kegiatan_user')
            ? $user->kegiatanDiikuti()->where('kegiatan_id',$kegiatan->id)->first()?->pivot
            : null;
        return view('anggota.kegiatan.show', compact('kegiatan','pivot'));
    }

    public function register(Kegiatan $kegiatan)
    {
        abort_unless($kegiatan->is_published, 404);
        if (!Schema::hasTable('kegiatan_user')) {
            return back()->with('error','Fitur pendaftaran belum siap. Jalankan migrasi terlebih dahulu.');
        }
        if (optional($kegiatan->waktu_mulai)->isPast()) {
            return back()->with('error','Kegiatan sudah mulai/selesai, pendaftaran ditutup.');
        }
        $user = Auth::user();
        $exists = $user->kegiatanDiikuti()->where('kegiatan_id',$kegiatan->id)->exists();
        if ($exists) { return back()->with('info','Anda sudah terdaftar pada kegiatan ini.'); }

        $user->kegiatanDiikuti()->attach($kegiatan->id, [
            'status' => 'registered',
            'kode' => strtoupper(Str::random(8)),
            'registered_at' => now(),
        ]);

        return back()->with('success','Pendaftaran berhasil. Sampai jumpa di kegiatan!');
    }

    public function unregister(Kegiatan $kegiatan)
    {
        abort_unless($kegiatan->is_published, 404);
        if (!Schema::hasTable('kegiatan_user')) {
            return back()->with('error','Fitur pendaftaran belum siap. Jalankan migrasi terlebih dahulu.');
        }
        $user = Auth::user();
        $user->kegiatanDiikuti()->updateExistingPivot($kegiatan->id, [ 'status' => 'canceled' ]);
        $user->kegiatanDiikuti()->detach($kegiatan->id);
        return back()->with('success','Pendaftaran Anda dibatalkan.');
    }

    public function ics(Kegiatan $kegiatan)
    {
        abort_unless($kegiatan->is_published, 404);

        $start = optional($kegiatan->waktu_mulai)?->copy()->utc()->format('Ymd\THis\Z');
        $end = optional($kegiatan->waktu_selesai ?? $kegiatan->waktu_mulai?->copy()->addHours(1))?->copy()->utc()->format('Ymd\THis\Z');
        $uid = 'kegiatan-'.$kegiatan->id.'@app.local';

        $ics = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//App//Kegiatan//ID\r\nCALSCALE:GREGORIAN\r\nMETHOD:PUBLISH\r\nBEGIN:VEVENT\r\nUID:$uid\r\nSUMMARY:".addcslashes($kegiatan->judul, ",;\\") ."\r\nDTSTART:$start\r\nDTEND:$end\r\nLOCATION:".addcslashes((string)$kegiatan->lokasi, ",;\\") ."\r\nDESCRIPTION:".addcslashes(strip_tags((string)$kegiatan->deskripsi), ",;\\\n") ."\r\nEND:VEVENT\r\nEND:VCALENDAR\r\n";

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="kegiatan-'.($kegiatan->slug ?: $kegiatan->id).'.ics"',
        ]);
    }
}
