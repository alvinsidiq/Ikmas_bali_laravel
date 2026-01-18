<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $totalAnggota = 0;
        $users = collect();

        if (Schema::hasTable('users')) {
            $query = Schema::hasTable('model_has_roles')
                ? User::role('anggota')
                : User::query();

            $users = $query
                ->with('profile')
                ->orderBy('name')
                ->paginate(15)
                ->withQueryString();

            $totalAnggota = $users->total();
        }

        return view('bendahara.anggota.index', [
            'totalAnggota' => $totalAnggota,
            'users' => $users,
        ]);
    }
}
