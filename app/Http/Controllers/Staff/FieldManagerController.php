<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\FieldManager;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        return view('staff.field_manager.index', compact('field_managers'));
    }

    public function create()
    {
        return view('staff.field_manager.create');
    }
    public function store(Request $request)
    {
        // dd($request->all());
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:field_manager,email|max:255',
            'mobile' => 'required|unique:field_manager,mobile_no|min:10|max:10',
            'image' => 'nullable|mimes:jpeg,jpg,png|max:2048',
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
            $imagename = 'null';
        }

        $owner = FieldManager::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile_no' => $request->input('mobile'),
            'image' => $imagename
        ]);
        Toastr::success('message', 'Owner add successfully.');
        return redirect()->route('staff.field_manager.index');
    }

    public function edit($id)
    {
        $field_manager = FieldManager::find($id);
        return view('staff.field_manager.edit', compact('field_manager'));
    }
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'email' => 'required|unique:staff,email,' . $id,
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
        $owner->save();

        Toastr::success('message', 'Owner updated successfully.');
        return redirect()->route('staff.field_manager.index');
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
    public function showLocation($id)
    {
        $fieldManager = FieldManager::findOrFail($id);
        $user=User::where('id',2)->first();
        // dd($user);
        return view('staff.field_manager.field_manager_location', compact('fieldManager','user'));
    }
}