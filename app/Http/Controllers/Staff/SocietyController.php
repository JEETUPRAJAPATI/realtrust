<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Cities;
use App\Models\Locality;
use App\Models\Society;
use Illuminate\Http\Request;

use Yoeunes\Toastr\Facades\Toastr;

class SocietyController extends Controller
{
    public function index()
    {
        $societies = Society::with('locality')->withCount('properties')->get();
        // dd($societies);
        return view('staff.society.index', compact('societies'));
    }


    public function create()
    {
        $localities =  Locality::get();
        //  dd($localities);
        return view('staff.society.create', compact('localities'));
    }


    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'locality' => 'required',
            'name' => 'required'
        ]);
        $Society = new Society();
        $Society->name = $request->name;
        $Society->locality_id = $request->locality;
        $Society->save();
        Toastr::success('message', 'Society add successfully.');
        return redirect()->route('staff.society.index');
    }


    public function show($id)
    {
        $society = Society::where('id', $id)->firstOrFail();
        return view('staff.society.show', compact('society'));
    }


    public function edit($id)
    {
        $socity =Society::with('locality')->where('id', $id)->firstOrFail();
        // dd($society);
        return view('staff.society.edit', compact('socity'));
    }


    public function update(Request $request, $id)
    {
        // dd($request->all(), $id);
        $request->validate([
            'name' => 'required'
        ]);
        $society = Society::where('id', $id)->firstOrFail();
        $society->name = $request->name;
        $society->update();
        Toastr::success('message', 'Society updated successfully.');
        return redirect()->route('staff.society.index');
    }

    public function destroy($id)
    {
        $society = Society::where('id', $id)->firstOrFail();
        $society->delete();
        Toastr::success('message', 'Society deleted successfully.');
        return back();
    }
}
