<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Support\Str;
use App\Models\Property;

class OwnerController extends Controller
{
    public function index()
    {
        $owners = Owner::orderBy('id', 'desc')->get();
        return view('admin.owner.index', compact('owners'));
    }

    public function create()
    {
        return view('admin.owner.create');
    }
    public function store(Request $request)
    {
        // dd($request->all());
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:owners,email|max:255',
            'mobile' => 'required|digits:10|unique:owners,mobile_no',
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

            if (!Storage::disk('public')->exists('owners')) {
                Storage::disk('public')->makeDirectory('owners');
            }

            $owner =  Image::make($image)->stream();
            Storage::disk('public')->put('owners/' . $imagename, $owner);
        } else {
            $imagename = null;
        }

        $owner = Owner::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile_no' => $request->input('mobile'),
            'image' => $imagename
        ]);
        Toastr::success('message', 'Owner add successfully.');
        return redirect()->route('admin.owner.index');
    }

    public function edit($id)
    {
        $owners = Owner::find($id);
        return view('admin.owner.edit', compact('owners'));
    }
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|mimes:jpeg,jpg,png,webp,avif',
            'mobile' => 'required|digits:15|unique:owners,mobile_no,' . $id,
            'email' => 'required|unique:owners,email,' . $id,
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
        $owner = Owner::find($id);

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('owners')) {
                Storage::disk('public')->makeDirectory('owners');
            }
            if (Storage::disk('public')->exists('owners/' . $owner->image)) {
                Storage::disk('public')->delete('owners/' . $owner->image);
            }
            $ownerImg = Image::make($image)->stream();
            Storage::disk('public')->put('owners/' . $imagename, $ownerImg);
        } else {
            $imagename = $owner->image;
        }

        $owner->name = $request->name;
        $owner->image = $imagename;
        $owner->mobile_no = $request->mobile;
        $owner->email = $request->email;
        $owner->save();

        Toastr::success('message', 'Owner updated successfully.');
        return redirect()->route('admin.owner.index');
    }
    public function destroy(string $id)
    {
        $owner = Owner::find($id);
        $property = Property::where('owner_id', $owner->id)->get();
        if ($property->isNotEmpty()) {
            Toastr::error('Owner cannot be deleted as it has associated property.');
            return back();
        }
        if (Storage::disk('public')->exists('owners/' . $owner->image)) {
            Storage::disk('public')->delete('owners/' . $owner->image);
        }
        $owner->delete();
        Toastr::success('message', 'Owner deleted successfully.');
        return back();
    }
}
