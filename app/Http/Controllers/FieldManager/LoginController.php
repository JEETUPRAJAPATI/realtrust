<?php

namespace App\Http\Controllers\FieldManager;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yoeunes\Toastr\Facades\Toastr;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    // public function index()
    // {
    //     return view('field_manager.login');
    // }
    // public function login(Request $request)
    // {
    //     // dd($request->all());
    //     $credentials = $request->only('email', 'password');

    //     if (Auth::guard('field_manager')->attempt($credentials)) {
    //         $user = Auth::guard('field_manager')->user();
    //         return redirect()->route('field_manager.dashboard');
    //     }
    //     Toastr::error('Invalid credentials.', 'Error');
    //     return redirect()->route('field_manager');
    // }
    public function logout(Request $request)
    {
        $guard = null;
        if (Auth::guard(name: 'field_manager')->check()) {
            $guard = 'field_manager';
        }
        if ($guard) {
            Auth::guard($guard)->logout();
        }
        return redirect()->route('login');
    }
}
