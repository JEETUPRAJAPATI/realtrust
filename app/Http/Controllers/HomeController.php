<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Auth::check()) {
            if (Auth::user()->role_id == 1) {
                return redirect()->route('admin.dashboard');
            } elseif (Auth::user()->role_id == 2) {
                return redirect()->route('staff.dashboard');
            }
        }
        return redirect()->route('login');
    }
}
