<?php
namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index() {
        return view('anggota.dashboard');
    }
}
