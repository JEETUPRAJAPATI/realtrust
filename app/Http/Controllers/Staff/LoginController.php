<?php

namespace App\Http\Controllers\Staff;

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

    // public function index()
    // {
    //     return view('staff.login');
    // }
    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');
    //     if (Auth::guard('staff')->attempt($credentials)) {
    //         $user = Auth::guard('staff')->user();
    //         return redirect()->route('staff.dashboard');
    //     }
    //     return redirect()->route('login')
    //         ->with('error', 'Invalid credentials');
    // }
    // public function logout(Request $request)
    // {
    //     $guard = null;
    //     if (Auth::guard('admin')->check()) {
    //         $guard = 'admin';
    //     }
    //     if (Auth::guard('staff')->check()) {
    //         $guard = 'staff';
    //     }
    //     if ($guard) {
    //         Auth::guard($guard)->logout();
    //     }
    //     // Show success message using Toastr
    //     Toastr::success('You have been logged out successfully.', 'Success');
    //     // Redirect the user to the desired route
    //     return redirect()->route('login');
    // }

    // public function index()
    // {
    //     return view('staff.login');
    // }
    // public function login(Request $request)
    // {
    //     // dd($request->all());
    //     $credentials = $request->only('email', 'password');

    //     if (Auth::guard('staff')->attempt($credentials)) {
    //         $user = Auth::guard('staff')->user();
    //         // dd($user);
    //         return redirect()->route('staff.dashboard');
    //     }
    //     Toastr::error('Invalid credentials.', 'Error');

    //     // Redirect the user to the desired route
    //     return redirect()->route('staff');
    // }
    public function logout(Request $request)
    {
        $guard = null;
        if (Auth::guard('staff')->check()) {
            $guard = 'staff';
        }
        if ($guard) {
            Auth::guard($guard)->logout();
        }
        // Show success message using Toastr
        // Toastr::success('You have been logged out successfully.', 'Success');
        // Redirect the user to the desired route
        return redirect()->route('login');
    }
}
