<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Support\Str;
use App\Models\UserDocument;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->get();
        return view('staff.users.index', compact('users'));
    }

    public function create()
    {
        return view('staff.users.create');
    }
    public function store(Request $request)
    {
        //  dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|unique:users|max:255',
            'mobile' => 'required|numeric|digits:10',
            // 'image' => 'nullable|mimes:jpeg,jpg,png'
        ]);

        $image = $request->file('image');
        $slug  = Str::slug($request->name);

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('users')) {
                Storage::disk('public')->makeDirectory('users');
            }

            $userimg = Image::make($image)->stream();
            Storage::disk('public')->put('users/' . $imagename, $userimg);
        } else {
            $imagename = NULL;
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile_no' => $request->input('mobile'),
            'image' => $imagename,
            'role_id' => '3'
        ]);
        // dd($user);

        Toastr::success('message', 'User created successfully.');
        return redirect()->route('staff.user.index');
    }

    public function edit($id)
    {
        $user = User::find($id);
        return view('staff.users.edit', compact('user'));
    }
    public function update(Request $request, string $id)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|mimes:jpeg,jpg,png',
            'agreement' => 'nullable|mimes:pdf',
        ]);

        $image = $request->file('image');
        $slug  = Str::slug($request->name);
        $user = User::find($id);
        // dd($user);
        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('users')) {
                Storage::disk('public')->makeDirectory('users');
            }
            if (Storage::disk('public')->exists('users/' . $user->image)) {
                Storage::disk('public')->delete('users/' . $user->image);
            }
            $userimg = Image::make($image)->stream();
            Storage::disk('public')->put('users/' . $imagename, $userimg);
        } else {
            $imagename = $user->image;
        }
        $file = $request->file('agreement');
        if (isset($file)) {
            $currentDate = Carbon::now()->toDateString();
            $agreementName = 'agreement-' . $user->id . '-' . $currentDate . '.' . $file->getClientOriginalExtension();

            // Create directory if it doesn't exist
            if (!Storage::disk('public')->exists('users/agreement')) {
                Storage::disk('public')->makeDirectory('users/agreement');
            }

            // Delete old agreement if exists
            if ($user->agreement && Storage::disk('public')->exists('users/agreement/' . $user->agreement)) {
                Storage::disk('public')->delete('users/agreement/' . $user->agreement);
            }

            // Store new agreement
            $filePath = $file->storeAs('users/agreement', $agreementName, 'public');
            $user->agreement = $filePath;
        } else {
            $agreementName = $user->agreement;
        }
        $user->name = $request->name;
        $user->image = $imagename;
        $user->agreement = $agreementName;
        $user->save();

        Toastr::success('message', 'User updated successfully.');
        return redirect()->route('staff.user.index');
    }
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (Storage::disk('public')->exists('users/' . $user->image)) {
            Storage::disk('public')->delete('users/' . $user->image);
        }
        $user->delete();
        Toastr::success('message', 'User deleted successfully.');
        return back();
    }

    public function getUserDetails($id)
    {
        $user = User::with('userDocuments')->find($id);
        // dd($user);
        return view('staff.users.verification', compact('user'));
    }

    public function updateStatus(Request $request, $id)
    {

        $request->validate([
            'verification' => 'required|boolean',
        ]);
        // dd($request->all());
        try {
            $userdoc = UserDocument::where('user_id', $id)->first();
            $user = User::findOrFail($id);

            // Check if all required documents are available
            if (empty($userdoc->aadhaar_card) || empty($userdoc->pan_card) || empty($userdoc->employee_id)) {
                Toastr::error('All required documents must be uploaded before verification.', 'Error');
                return back();
            }

            // If all documents are available, proceed with the verification
            $user->verification = $request->verification;
            $user->save();

            Toastr::success('Verification status updated successfully.', 'Success');
            return back();
        } catch (\Exception $e) {
            // Handle the error if the user is not found or any other exception occurs
            Toastr::error('User not found.', 'Error');
            return back();
        }
    }
}
