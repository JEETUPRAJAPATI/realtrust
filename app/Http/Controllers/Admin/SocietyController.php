<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Locality;
use App\Models\Society;
use Yoeunes\Toastr\Facades\Toastr;
class SocietyController extends Controller
{
    public function index()
    {
        $societies = Society::with('locality')->withCount('properties')->get();
        // dd($societies);
        return view('admin.society.index', compact('societies'));
    }


    public function create()
    {
        $localities =  Locality::get();
        //  dd($localities);
        return view('admin.society.create', compact('localities'));
    }


    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'locality' => 'required',
            'name' => 'required',
            'embeded_map' => 'required'
        ]);
        $Society = new Society();
        $Society->name = $request->name;
        $Society->locality_id = $request->locality;
        $Society->embeded_map = $request->embeded_map;
        $Society->save();
        Toastr::success('message', 'Society add successfully.');
        return redirect()->route('admin.society.index');
    }


    public function show($id)
    {
        $society = Society::where('id', $id)->firstOrFail();
        return view('admin.society.show', compact('society'));
    }


    public function edit($id)
    {
        $socity =Society::with('locality')->where('id', $id)->firstOrFail();
        // dd($society);
        return view('admin.society.edit', compact('socity'));
    }


    public function update(Request $request, $id)
    {
        // dd($request->all(), $id);
        $request->validate([
            'name' => 'required',
            'embeded_map' => 'required'
        ]);
        $society = Society::where('id', $id)->firstOrFail();
        $society->name = $request->name;
        $society->embeded_map = $request->embeded_map;
        $society->update();
        Toastr::success('message', 'Society updated successfully.');
        return redirect()->route('admin.society.index');
    }

    public function destroy($id)
    {
        $society = Society::where('id', $id)->firstOrFail();
        $society->delete();
        Toastr::success('message', 'Society deleted successfully.');
        return back();
    }
}
