<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yoeunes\Toastr\Facades\Toastr;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    public function index()
    {
        if (Auth::guard('staff')->check()) {
            return redirect()->route('staff.dashboard');
        } elseif (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::guard('field_manager')->check()) {
            return redirect()->route('field_manager.dashboard');
        } else {
            return view('auth.login');
        }
    }
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        // dd($credentials);
        if (Auth::guard('admin')->attempt($credentials)) {
            // dd('admin');
            return redirect()->route('admin.dashboard');
        }

        if (Auth::guard('field_manager')->attempt($credentials)) {
            $user = Auth::guard('field_manager')->user();
            return redirect()->route('field_manager.dashboard');
        }

        if (Auth::guard('staff')->attempt($credentials)) {
            $user = Auth::guard('staff')->user();
            // dd($user);
            return redirect()->route('staff.dashboard');
        }
        Toastr::error('Invalid credentials.', 'Error');
        return redirect()->route('login');
    }
    public function logout(Request $request)
    {
        $guard = null;
        if (Auth::guard('admin')->check()) {
            $guard = 'admin';
        }
        if ($guard) {
            Auth::guard($guard)->logout();
        }
        // Show success message using Toastr
        Toastr::success('You have been logged out successfully.', 'Success');
        // Redirect the user to the desired route
        return redirect()->route('login');
    }
}
