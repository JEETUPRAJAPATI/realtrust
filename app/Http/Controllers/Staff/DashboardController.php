<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\FieldManager;
use App\Models\Notifications;
use App\Models\Owner;
use App\Models\Post;
use App\Models\Property;
use App\Models\Staff;
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
        // $users = Auth::guard('staff')->user();
        // return view('staff.dashboard', compact('users'));

        $propertycount = Property::count();
        $fieldManagerCount     = FieldManager::count();
        $ownercount  = Owner::count();
        $usercount     = User::count();

        $properties    = Property::latest()->with('user', 'amenities')->take(5)->get();
        $properties_map = Property::where('status', 'Active')->with('gallery')
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get();
        foreach ($properties_map as $property) {
            $property->image = $property->image_url; // Use accessor for image URL
        }
        // dd($properties_map);
        $users=User::take(10)->get();

        return view('staff.dashboard', compact(
            'propertycount',
            'usercount',
            'fieldManagerCount',
            'ownercount',
            'properties',
            'properties_map',
            'users'
        ));
    }

    public function changePassword()
    {
        return view('staff.settings.changepassword');
    }

    public function changePasswordUpdate(Request $request)
    {

        if (!(Hash::check($request->get('currentpassword'), Auth::guard('staff')->user()->password))) {

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

        $user = Auth::guard('staff')->user();
        $user->password = bcrypt($request->get('newpassword'));
        $user->save();

        Toastr::success('message', 'Password changed successfully.');
        return redirect()->route('staff.dashboard');
    }
    public function profile()
    {
        $profile = Auth::guard('staff')->user();
        return view('staff.settings.profile', compact('profile'));
    }
    public function profileUpdate(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'name'      => 'required',
            'email'     => 'required|email',
            'image'     => 'image|mimes:jpeg,jpg,png'
        ]);

        $user = Staff::find(Auth::guard('staff')->id());
        // dd($request->all());
        $image = $request->file('image');
        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = 'staff-' . Auth::guard('staff')->id() . '-' . $currentDate . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('staff')) {
                Storage::disk('public')->makeDirectory('staff');
            }
            if (Storage::disk('public')->exists('staff/' . $user->image) && $user->image != 'default.png') {
                Storage::disk('public')->delete('staff/' . $user->image);
            }
            // $userimage = $image::make($image)->stream();
            $userimage = Image::make($image)->stream();
            Storage::disk('public')->put('staff/' . $imagename, $userimage);
        } else {
            $imagename = $user->image;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->image = $imagename;

        $user->save();
        return redirect()->route('staff.dashboard');
    }
    public function markAsRead($id)
    {
        $notification = Auth::guard('staff')->user()->notifications()->findOrFail($id);
        if ($notification) {
            $notification->markAsRead();
        }

        return redirect()->back()->with('status', 'Notification marked as read');
    }
}
