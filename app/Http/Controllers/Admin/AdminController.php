<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index()
    {
        $users = Admin::orderBy('id', 'desc')->get();
        return view('admin.admins.index', compact('users'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|unique:users|max:255',
            'password' => 'required|string|min:6',
            'image' => 'nullable|mimes:jpeg,jpg,png'
        ]);

        $image = $request->file('image');
        $slug  = Str::slug($request->name);

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('admin')) {
                Storage::disk('public')->makeDirectory('admin');
            }

            $user =  Image::make($image)->stream();
            Storage::disk('public')->put('admin/' . $imagename, $user);
        } else {
            $imagename = NULL;
        }
        // dd($request->all());
        $user = Admin::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);
        // dd($user);

        Toastr::success('message', 'Admin add successfully.');
        return redirect()->route('admin.admin-list.index');
    }

    public function edit($id)
    {
        $admin = Admin::find($id);
        return view('admin.admins.edit', compact('admin'));
    }
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|mimes:jpeg,jpg,png'
        ]);

        $image = $request->file('image');
        $slug  = Str::slug($request->name);
        $admin = Admin::find($id);

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('admin')) {
                Storage::disk('public')->makeDirectory('admin');
            }
            if (Storage::disk('public')->exists('admin/' . $admin->image)) {
                Storage::disk('public')->delete('admin/' . $admin->image);
            }
            $adminimg = Image::make($image)->stream();
            Storage::disk('public')->put('admin/' . $imagename, $adminimg);
        } else {
            $imagename = $admin->image;
        }

        $admin->name = $request->name;
        $admin->image = $imagename;
        $admin->save();

        Toastr::success('message', 'Admin updated successfully.');
        return redirect()->route('admin.admin-list.index');
    }
    public function destroy(string $id)
    {
        $admin = Admin::find($id);
        if (Storage::disk('public')->exists('admin/' . $admin->image)) {
            Storage::disk('public')->delete('admin/' . $admin->image);
        }
        $admin->delete();
        Toastr::success('message', 'Admin deleted successfully.');
        return back();
    }
}
