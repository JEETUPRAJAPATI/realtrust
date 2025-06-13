<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Support\Str;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FeatureController extends Controller
{
    public function index()
    {
        $features = Feature::latest()->get();
        return view('admin.features.index', compact('features'));
    }


    public function create()
    {
        return view('admin.features.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:features|max:255',
              'image' => 'required|mimes:jpeg,jpg,png'
        ]);
        $image = $request->file('image');
        $slug= Str::slug($request->name);
       if (isset($image)) {
           $currentDate = Carbon::now()->toDateString();
           $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

           if (!Storage::disk('public')->exists('feature')) {
               Storage::disk('public')->makeDirectory('feature');
           }
           $user =  Image::make($image)->stream();
           Storage::disk('public')->put('feature/' . $imagename, $user);
       } else {
           $imagename = 'default.png';
       }
        $tag = new Feature();
        $tag->name = $request->name;
        $tag->image = $imagename;
        $tag->slug =$slug;
        $tag->save();

        Toastr::success('message', 'Feature created successfully.');
        return redirect()->route('admin.features.index');
    }


    public function edit($id)
    {
        $feature = Feature::find($id);

        return view('admin.features.edit', compact('feature'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'image' => 'required|mimes:jpeg,jpg,png'
        ]);

        $feature = Feature::find($id);

        $slug=Str::slug($request->name);
        $image = $request->file('image');
        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('feature')) {
                Storage::disk('public')->makeDirectory('feature');
            }
            if (Storage::disk('public')->exists('feature/' . $feature->image)) {
                Storage::disk('public')->delete('feature/' . $feature->image);
            }
            $userimg = Image::make($image)->stream();
            Storage::disk('public')->put('feature/' . $imagename, $userimg);
        } else {
            $imagename = $feature->image;
        }


        $feature->name = $request->name;
        $feature->slug = $slug;
        $feature->image = $imagename;
        $feature->save();

        Toastr::success('message', 'Feature updated successfully.');
        return redirect()->route('admin.features.index');
    }


    public function destroy($id)
    {
        $feature = Feature::find($id);
        if ($feature) {
            // Detach related feature first, then delete the main feature
            if (Storage::disk('public')->exists('feature/' . $feature->image)) {
                Storage::disk('public')->delete('feature/' . $feature->image);
            }
            $feature->properties()->detach();
            $feature->delete();

            Toastr::success('feature deleted successfully.', 'Success');
        } else {
            Toastr::error('feature not found.', 'Error');
        }

        return back();
    }
}
