<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Support\Str;
use App\Models\StaffPermission;

class StaffController extends Controller
{

    public function index()
    {
        $staffs = Staff::orderBy('id', 'desc')->get();
        return view('admin.staff.index', compact('staffs'));
    }

    public function create()
    {
        // dd('calling');
        return view('admin.staff.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|unique:staff|max:255',
            'mobile' => 'required|digits:10|unique:staff,mobile_no',
            'image' => 'nullable|mimes:jpeg,jpg,png,webp,avif'
        ]);

        $image = $request->file('image');
        $slug  = Str::slug($request->name);

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('staff')) {
                Storage::disk('public')->makeDirectory('staff');
            }
            $agent =  Image::make($image)->stream();
            Storage::disk('public')->put('staff/' . $imagename, $agent);
        } else {
            $imagename = 'default.png';
        }

        $staff = Staff::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile_no' => $request->input('mobile'),
            'image' => $imagename,
        ]);
        // dd($user);
        StaffPermission::create([
            'staff_id' => $staff->id,
        ]);
        Toastr::success('message', 'Staff created successfully.');
        return redirect()->route('admin.staff.index');
    }

    public function edit($id)
    {
        $staff = Staff::find($id);
        return view('admin.staff.edit', compact('staff'));
    }
    public function update(Request $request, string $id)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,avif|max:2048',
            'email' => 'required|unique:staff,email,' . $id,
            'mobile' => 'required|digits:15|unique:staff,mobile_no,' . $id,
        ]);

        $image = $request->file('image');
        $slug  = Str::slug($request->name);
        $staff = Staff::find($id);

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('staff')) {
                Storage::disk('public')->makeDirectory('staff');
            }
            if (Storage::disk('public')->exists('staff/' . $staff->image)) {
                Storage::disk('public')->delete('staff/' . $staff->image);
            }
            $agentImage =  Image::make($image)->stream();
            Storage::disk('public')->put('staff/' . $imagename, $agentImage);
        } else {
            $imagename = $staff->image;
        }
        $staff->name = $request->name;
        $staff->image = $imagename;
        $staff->mobile_no = $request->mobile;
        $staff->email = $request->email;
        $staff->save();

        Toastr::success('message', 'Staff updated successfully.');
        return redirect()->route('admin.staff.index');
    }
    public function destroy(string $id)
    {
        $staff = Staff::find($id);
        if (Storage::disk('public')->exists('staff/' . $staff->image)) {
            Storage::disk('public')->delete('staff/' . $staff->image);
        }
        $staff->delete();
        Toastr::success('message', 'Staff deleted successfully.');
        return back();
    }
    public function permission($id)
    {
        $permission = StaffPermission::where('staff_id', $id)->first();
        return view('admin.staff.permissions', compact('permission'));
    }
    public function updatePermission(Request $request)
    {
        $type = $request->type;
        $user = StaffPermission::where('staff_id', $request->id)->first();

        if ($user) {
            switch ($type) {
                case 'owner':
                    $user->owner = $request->status;
                    break;
                case 'owner_number':
                    $user->owner_number = $request->status;
                    break;
                case 'users':
                    $user->users = $request->status;
                    break;
                case 'property':
                    $user->property = $request->status;
                    break;
                case 'fieldManager_list':
                    $user->fieldManager_list = $request->status;
                    break;
                case 'schedule_visit':
                    $user->schedule_visit = $request->status;
                    break;
                case 'visiter_list':
                    $user->visiter_list = $request->status;
                    break;
                case 'recording':
                    $user->recording = $request->status;
                    break;
                case 'post_list':
                    $user->post_list = $request->status;
                    break;
                case 'inquiry_list':
                    $user->inquiry_list = $request->status;
                    break;
                case 'settings':
                    $user->settings = $request->status;
                    break;
            }

            $user->update();

            return response()->json([
                'success' => 200,
                'message' => 'Status updated successfully!',
                'data' => $user->fresh()
            ]);
        }

        return response()->json([
            'success' => 404,
            'message' => 'User not found'
        ]);
    }
    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $staff = Staff::find($id);
        $staff->password = Hash::make($request->password);
        $staff->save();
        Toastr::success('message', 'Password updated successfully.');
        return back();
    }
}
