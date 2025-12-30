<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAnggotaRequest;
use App\Http\Requests\Admin\UpdateAnggotaRequest;
use App\Models\User;
use App\Models\AnggotaProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AnggotaController extends Controller
{

    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $status = $request->get('status'); // 'aktif' | 'nonaktif' | null

        $users = User::role('anggota')
            ->with('profile')
            ->when($q, function($qr) use ($q){
                $qr->where(function($w) use ($q){
                    $w->where('name','like',"%$q%")
                      ->orWhere('email','like',"%$q%")
                      ->orWhereHas('profile', fn($p)=>$p->where('nama_lengkap','like',"%$q%")
                                                      ->orWhere('nik','like',"%$q%")
                                                      ->orWhere('phone','like',"%$q%"));
                });
            })
            ->when($status, function($qr) use ($status){
                $is = $status === 'aktif';
                $qr->whereHas('profile', fn($p)=>$p->where('is_active',$is));
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.anggota.index', compact('users','q','status'));
    }

    public function create()
    {
        return view('admin.anggota.create');
    }

    public function store(StoreAnggotaRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function() use ($data, $request) {
            $password = $data['password'] ?? 'password123';
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($password),
            ]);
            $user->assignRole('anggota');

            $profile = new AnggotaProfile([
                'nik' => $data['nik'] ?? null,
                'nama_lengkap' => $data['nama_lengkap'] ?? $data['name'],
                'phone' => $data['phone'] ?? null,
                'alamat' => $data['alamat'] ?? null,
                'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
                'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
                'pekerjaan' => $data['pekerjaan'] ?? null,
                'organisasi' => $data['organisasi'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'joined_at' => now(),
            ]);

            if ($request->hasFile('avatar')) {
                $profile->avatar_path = $request->file('avatar')->store('anggota/avatars','public');
            }

            $user->profile()->save($profile);
        });

        return redirect()->route('admin.anggota.index')->with('success','Anggota berhasil ditambahkan');
    }

    public function show(User $anggotum)
    {
        $anggotum->load('profile');
        return view('admin.anggota.show', ['anggota' => $anggotum]);
    }

    public function edit(User $anggotum)
    {
        $anggotum->load('profile');
        return view('admin.anggota.edit', ['anggota' => $anggotum]);
    }

    public function update(UpdateAnggotaRequest $request, User $anggotum)
    {
        $data = $request->validated();

        DB::transaction(function() use ($data, $request, $anggotum){
            $anggotum->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            if (!empty($data['password'])) {
                $anggotum->update(['password' => Hash::make($data['password'])]);
            }

            $p = $anggotum->profile ?: new AnggotaProfile(['user_id' => $anggotum->id]);
            $p->fill([
                'nik' => $data['nik'] ?? $p->nik,
                'nama_lengkap' => $data['nama_lengkap'] ?? $p->nama_lengkap,
                'phone' => $data['phone'] ?? $p->phone,
                'alamat' => $data['alamat'] ?? $p->alamat,
                'tanggal_lahir' => $data['tanggal_lahir'] ?? $p->tanggal_lahir,
                'jenis_kelamin' => $data['jenis_kelamin'] ?? $p->jenis_kelamin,
                'pekerjaan' => $data['pekerjaan'] ?? $p->pekerjaan,
                'organisasi' => $data['organisasi'] ?? $p->organisasi,
                'is_active' => $data['is_active'] ?? $p->is_active,
            ]);

            if ($request->hasFile('avatar')) {
                if ($p->avatar_path) Storage::disk('public')->delete($p->avatar_path);
                $p->avatar_path = $request->file('avatar')->store('anggota/avatars','public');
            }

            $anggotum->profile()->save($p);
        });

        return redirect()->route('admin.anggota.index')->with('success','Data anggota diperbarui');
    }

    public function destroy(User $anggotum)
    {
        // Nonaktifkan sebagai default behavior yang aman
        $anggotum->load('profile');
        if ($anggotum->profile) {
            $anggotum->profile->update(['is_active' => false]);
        }
        return redirect()->back()->with('success','Anggota dinonaktifkan');
    }

    // Aksi tambahan
    public function toggleActive(User $anggotum)
    {
        $anggotum->load('profile');
        if (!$anggotum->profile) {
            $anggotum->profile()->create(['is_active' => true, 'joined_at' => now()]);
        } else {
            $anggotum->profile->is_active = !$anggotum->profile->is_active;
            $anggotum->profile->save();
        }
        return redirect()->back()->with('success','Status anggota diperbarui');
    }

    public function resetPassword(Request $request, User $anggotum)
    {
        $request->validate(['password' => ['required','string','min:8']]);
        $anggotum->update(['password' => Hash::make($request->password)]);
        return redirect()->back()->with('success','Password direset');
    }
}