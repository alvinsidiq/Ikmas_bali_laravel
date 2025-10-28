<?php
namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index() {
        return view('bendahara.dashboard');
    }
}
