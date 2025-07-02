<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FieldManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Support\Str;

class FieldManagerController extends Controller
{
    public function index()
    {
        $field_managers = FieldManager::orderBy('id', 'desc')->get();

        return view('admin.field_manager.index', compact('field_managers'));
    }

    public function create()
    {
        return view('admin.field_manager.create');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:field_manager,email|max:255',
            'mobile' => 'required|digits:10|unique:field_manager,mobile_no',
            'image' => 'nullable|mimes:jpeg,jpg,png,webp,avif|max:2048',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Return back with errors and old input
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $image = $request->file('image');
        $slug  = Str::slug($request->name);

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('field_manager')) {
                Storage::disk('public')->makeDirectory('field_manager');
            }

            $owner =  Image::make($image)->stream();
            Storage::disk('public')->put('field_manager/' . $imagename, $owner);
        } else {
            $imagename = 'default.png';
        }

        $owner = FieldManager::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile_no' => $request->input('mobile'),
            'password' => Hash::make($request->input('password')),
            'image' => $imagename
        ]);
        Toastr::success('message', 'Owner add successfully.');
        return redirect()->route('admin.field_manager.index');
    }

    public function edit($id)
    {
        $field_manager = FieldManager::find($id);
        return view('admin.field_manager.edit', compact('field_manager'));
    }
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp,avif|max:2048',
            'mobile' => 'required|digits:15|unique:field_manager,mobile_no,' . $id,
            'email' => 'required|unique:field_manager,email,' . $id,
        ]);
        // Check if validation fails
        if ($validator->fails()) {
            // Return back with errors and old input
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $image = $request->file('image');
        $slug  = Str::slug($request->name);
        $owner = FieldManager::find($id);

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('field_manager')) {
                Storage::disk('public')->makeDirectory('field_manager');
            }
            if (Storage::disk('public')->exists('field_manager/' . $owner->image)) {
                Storage::disk('public')->delete('field_manager/' . $owner->image);
            }
            $ownerImg = Image::make($image)->stream();
            Storage::disk('public')->put('field_manager/' . $imagename, $ownerImg);
        } else {
            $imagename = $owner->image;
        }

        $owner->name = $request->name;
        $owner->image = $imagename;
        $owner->mobile_no = $request->mobile;
        $owner->email = $request->email;
        $owner->save();

        Toastr::success('message', 'Owner updated successfully.');
        return redirect()->route('admin.field_manager.index');
    }

    public function destroy(string $id)
    {
        $field_manager = FieldManager::find($id);
        if (Storage::disk('public')->exists('field_manager/' . $field_manager->image)) {
            Storage::disk('public')->delete('field_manager/' . $field_manager->image);
        }
        $field_manager->delete();
        Toastr::success('message', 'Owner deleted successfully.');
        return back();
    }


    public function fieldManagerPasswordUpdate(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $field_manager = FieldManager::find($id);
        // dd($request->all());
        // dd($field_manager);
        if (!$field_manager) {
            Toastr::error('Field Manager not found.');
            return back();
        }

        $field_manager->password = Hash::make($request->password);
        $field_manager->save();

        Toastr::success('Password updated successfully.');
        return back();
    }
}
