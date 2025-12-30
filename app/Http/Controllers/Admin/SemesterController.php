<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SemesterController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q'));
        $items = Semester::query()
            ->when($q, fn($qr)=>$qr->where(function($x) use ($q){
                $x->where('nama','like',"%$q%")
                  ->orWhere('tahun_ajaran','like',"%$q%");
            }))
            ->orderByDesc('is_active')
            ->latest('mulai')
            ->latest('id')
            ->paginate(12)->withQueryString();

        return View::file(
            resource_path('views/admin/semesters/index.blade.php'),
            compact('items','q')
        );
    }

    public function create()
    {
        return View::file(resource_path('views/admin/semesters/create.blade.php'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Semester::create($data);
        return redirect()->route('admin.semesters.index')->with('success','Semester ditambahkan.');
    }

    public function edit(Semester $semester)
    {
        return View::file(resource_path('views/admin/semesters/edit.blade.php'), compact('semester'));
    }

    public function update(Request $request, Semester $semester)
    {
        $data = $this->validated($request);
        // Only one active semester at a time
        if (!empty($data['is_active'])) {
            Semester::where('id','!=',$semester->id)->update(['is_active'=>false]);
        }
        $semester->update($data);
        return redirect()->route('admin.semesters.index')->with('success','Semester diperbarui.');
    }

    public function destroy(Semester $semester)
    {
        $semester->delete();
        return redirect()->route('admin.semesters.index')->with('success','Semester dihapus.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'nama' => ['required','string','max:255'],
            'tahun_ajaran' => ['nullable','string','max:50'],
            'mulai' => ['nullable','date'],
            'selesai' => ['nullable','date','after_or_equal:mulai'],
            'is_active' => ['nullable','boolean'],
        ]);
    }
}
