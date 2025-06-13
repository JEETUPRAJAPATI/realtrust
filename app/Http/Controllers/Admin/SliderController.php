<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Yoeunes\Toastr\Facades\Toastr;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $sliders = Slider::latest()->get();
        return view('admin.sliders.index', compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.sliders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'title' => 'required|unique:sliders|max:255',
            'image' => 'required|mimes:jpeg,jpg,png,webp,gif,bmp,tiff,svg'
        ]);

        $image = $request->file('image');
        $slug  = Str::slug($request->title);

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('slider')) {
                Storage::disk('public')->makeDirectory('slider');
            }
            $slider = Image::make($image)->stream();
            Storage::disk('public')->put('slider/' . $imagename, $slider);
        } else {
            $imagename = 'default.png';
        }

        $slider = new Slider();
        $slider->title = $request->title;
        $slider->image = $imagename;
        $slider->save();

        Toastr::success('message', 'Slider created successfully.');
        return redirect()->route('admin.sliders.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $slider = Slider::find($id);

        return view('admin.sliders.edit', compact('slider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'image' => 'mimes:jpeg,jpg,png'
        ]);

        $image = $request->file('image');
        $slug  = Str::slug($request->title);
        $slider = Slider::find($id);

        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = $slug . '-' . $currentDate . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('slider')) {
                Storage::disk('public')->makeDirectory('slider');
            }
            if (Storage::disk('public')->exists('slider/' . $slider->image)) {
                Storage::disk('public')->delete('slider/' . $slider->image);
            }
            $sliderimg = Image::make($image)->stream();
            Storage::disk('public')->put('slider/' . $imagename, $sliderimg);
        } else {
            $imagename = $slider->image;
        }

        $slider->title = $request->title;
        $slider->image = $imagename;
        $slider->save();

        Toastr::success('message', 'Slider updated successfully.');
        return redirect()->route('admin.sliders.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $slider = Slider::find($id);

        if (Storage::disk('public')->exists('slider/' . $slider->image)) {
            Storage::disk('public')->delete('slider/' . $slider->image);
        }

        $slider->delete();

        Toastr::success('message', 'Slider deleted successfully.');
        return back();
    }
}
