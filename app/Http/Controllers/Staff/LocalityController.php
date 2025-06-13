<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Cities;
use App\Models\Locality;
use Yoeunes\Toastr\Facades\Toastr;
class LocalityController extends Controller
{
    public function index()
    {
        $localities = Locality::with(['state', 'city'])->where('state_id', 4026)
        ->where('cities_id', 57933)->withCount('properties')->get();
        // dd($localities);
        return view('staff.locality.index', compact('localities'));
    }


    public function create()
    {
        $stateWithCity = Cities::with('state')->where('city_id', 57933)
    ->first();
    // dd($stateWithCity);
        return view('staff.locality.create', compact('stateWithCity'));
    }


    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'locality' => 'required'
        ]);
        $Locality = new Locality();
        $Locality->name = $request->locality;
        $Locality->save();
        Toastr::success('message', 'Locality add successfully.');
        return redirect()->route('staff.locality.index');
    }


    public function show($id)
    {
        $localities = Locality::where('id', $id)->firstOrFail();
        return view('staff.locality.show', compact('localities'));
    }


    public function edit($id)
    {
        $localities = Locality::with(['state', 'city'])->where('state_id', 4026)
        ->where('cities_id', 57933)->where('id', $id)->firstOrFail();

        return view('staff.locality.edit', compact('localities'));
    }


    public function update(Request $request, $id)
    {
        // dd($request->all(), $id);
        $request->validate([
            'locality'      => 'required'
        ]);
        $Locality = Locality::where('id', $id)->firstOrFail();
        $Locality->name = $request->locality;
        $Locality->update();
        Toastr::success('message', 'locality updated successfully.');
        return redirect()->route('staff.locality.index');
    }

    public function destroy($id)
    {
        $locality = Locality::where('id', $id)->firstOrFail();
        $locality->delete();
        Toastr::success('message', 'Locality deleted successfully.');
        return back();
    }
}
