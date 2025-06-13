<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenities;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class AmenitiesController extends Controller
{
    public function index()
    {
        $amenities = Amenities::latest()->get();
        // dd($amenities);
        return view('admin.amenities.index', compact('amenities'));
    }


    public function create()
    {
        return view('admin.amenities.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:amenities|max:255',
            'image' => 'required|mimes:jpeg,jpg,png'
        ]);

        $image = $request->file('image');
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $count = 1;

        // Check if the slug already exists and append a number if necessary
        while (Amenities::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('amenities')) {
                Storage::disk('public')->makeDirectory('amenities');
            }
            $user =  Image::make($image)->stream();
            Storage::disk('public')->put('amenities/' . $imagename, $user);
        } else {
            $imagename = 'default.png';
        }
        $tag = new Amenities();
        $tag->name = $request->name;
        $tag->image = $imagename;
        $tag->slug = $slug;
        $tag->save();

        Toastr::success('message', 'Amenities created successfully.');
        return redirect()->route('admin.amenities.index');
    }


    public function edit($id)
    {
        $feature = Amenities::find($id);

        return view('admin.amenities.edit', compact('feature'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'image' => 'nullable|mimes:jpeg,jpg,png'
        ]);
       
        $amenities = Amenities::find($id);
        $baseSlug = Str::slug($request->name);

        // Check if the name has changed
        if ($amenities->name !== $request->name) {
            $slug = $baseSlug;
            $count = 1;

            // Ensure unique slug in the database
            while (Amenities::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }
        } else {
            $slug = $amenities->slug; // Keep existing slug if name is unchanged
        }

        $image = $request->file('image');
        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('amenities')) {
                Storage::disk('public')->makeDirectory('amenities');
            }
            if (Storage::disk('public')->exists('amenities/' . $amenities->image)) {
                Storage::disk('public')->delete('amenities/' . $amenities->image);
            }
            $userimg = Image::make($image)->stream();
            Storage::disk('public')->put('amenities/' . $imagename, $userimg);
        } else {
            $imagename = $amenities->image;
        }
        $amenities->name = $request->name;
        $amenities->slug =$slug;
        $amenities->image = $imagename;
        $amenities->save();

        Toastr::success('message', 'Amenities updated successfully.');
        return redirect()->route('admin.amenities.index');
    }


    public function destroy($id)
    {
        $amenities = Amenities::find($id);

        if ($amenities) {
            // Detach related amenities first, then delete the main amenities
            if (Storage::disk('public')->exists('amenities/' . $amenities->image)) {
                Storage::disk('public')->delete('amenities/' . $amenities->image);
            }
            $amenities->properties()->detach();
            $amenities->delete();

            Toastr::success('Amenities deleted successfully.', 'Success');
        } else {
            Toastr::error('Amenities not found.', 'Error');
        }

        return back();
    }
}
