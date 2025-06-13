<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        return view('admin.locality.index', compact('localities'));
    }


    public function create()
    {
        $stateWithCity = Cities::with('state')->where('city_id', 57933)
    ->first();
    // dd($stateWithCity);
        return view('admin.locality.create', compact('stateWithCity'));
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
        return redirect()->route('admin.locality.index');
    }


    public function show($id)
    {
        $localities = Locality::where('id', $id)->firstOrFail();
        return view('admin.locality.show', compact('localities'));
    }


    public function edit($id)
    {
        $localities = Locality::with(['state', 'city'])->where('state_id', 4026)
        ->where('cities_id', 57933)->where('id', $id)->firstOrFail();

        return view('admin.locality.edit', compact('localities'));
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
        return redirect()->route('admin.locality.index');
    }

    public function destroy($id)
    {
        $locality = Locality::where('id', $id)->firstOrFail();
        $locality->delete();
        Toastr::success('message', 'Locality deleted successfully.');
        return back();
    }

    public function getSocieties(Request $request)
    {
        $request->validate([
            'locality_id' => 'required|exists:localities,id'
        ]);

        $locality = Locality::find($request->locality_id);

        if ($locality) {
            $societies = $locality->societies; // Using the Eloquent relationship
            return response()->json($societies, 200);
        }

        return response()->json(['message' => 'No societies found'], 404);
    }
}
