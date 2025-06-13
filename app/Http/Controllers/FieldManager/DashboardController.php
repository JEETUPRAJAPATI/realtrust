<?php

namespace App\Http\Controllers\FieldManager;

use App\Http\Controllers\Controller;
use App\Models\FieldManager;
use App\Models\Owner;
use App\Models\Post;
use App\Models\Property;
use App\Models\ScheduleVisit;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yoeunes\Toastr\Facades\Toastr;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $id = Auth::guard('field_manager')->user()->id;

        // Get the 5 most recent visits for the field manager
        $field_managers = ScheduleVisit::with('property', 'user', 'owner')
            ->where('field_manager_id', $id)
            ->orderBy('id', 'desc')
            ->take(5) // Limit to 5 recent visits
            ->get();
        // dd($field_managers);
        $ownercount = ScheduleVisit::where('field_manager_id', $id)->count();

     return view('field_manager.dashboard', compact('field_managers', 'ownercount'));
    }


    public function changePassword()
    {
        return view('field_manager.settings.changepassword');
    }

    public function changePasswordUpdate(Request $request)
    {

        if (!(Hash::check($request->get('currentpassword'), Auth::guard('field_manager')->user()->password))) {

            Toastr::error('message', 'Your current password does not matches with the password you provided! Please try again.');
            return redirect()->back();
        }
        if (strcmp($request->get('currentpassword'), $request->get('newpassword')) == 0) {

            Toastr::error('message', 'New Password cannot be same as your current password! Please choose a different password.');
            return redirect()->back();
        }

        $this->validate($request, [
            'currentpassword' => 'required',
            'newpassword' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::guard('field_manager')->user();
        $user->password = bcrypt($request->get('newpassword'));
        $user->save();

        Toastr::success('message', 'Password changed successfully.');
        return redirect()->route('field_manager.dashboard');
    }
    public function profile()
    {
        $profile = Auth::guard('field_manager')->user();
        return view('field_manager.settings.profile', compact('profile'));
    }
    public function profileUpdate(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'name'      => 'required',
            'email'     => 'required|email',
            'image'     => 'image|mimes:jpeg,jpg,png'
        ]);

        $user = FieldManager::find(Auth::guard('field_manager')->id());
        // dd($request->all());
        $image = $request->file('image');
        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = 'field_manager-' . Auth::guard('field_manager')->id() . '-' . $currentDate . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('field_manager')) {
                Storage::disk('public')->makeDirectory('field_manager');
            }
            if (Storage::disk('public')->exists('field_manager/' . $user->image) && $user->image != 'default.png') {
                Storage::disk('public')->delete('field_manager/' . $user->image);
            }
            // $userimage = $image::make($image)->stream();
            $userimage = Image::make($image)->stream();
            Storage::disk('public')->put('field_manager/' . $imagename, $userimage);
        } else {
            $imagename = $user->image;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->image = $imagename;

        $user->save();

        return redirect()->route('field_manager.dashboard');
    }
}
