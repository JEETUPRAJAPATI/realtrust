<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Inquery;
use Illuminate\Http\Request;
use Yoeunes\Toastr\Facades\Toastr;

class InqueryController extends Controller
{
    public function index()
    {
        $inquerys = Inquery::orderBy('id', 'desc')->get();
        return view('staff.inquery.index', compact('inquerys'));
    }
    public function destroy(string $id)
    {
        $Inquery = Inquery::find($id);
        $Inquery->delete();
        Toastr::success('message', 'Inquery deleted successfully.');
        return back();
    }
    public function show($id)
    {
        $inquery = Inquery::where( 'id',$id)->firstOrFail();
        return view('staff.inquery.show', compact('inquery'));
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:resolved,pending',
        ]);
        $property = Inquery::findOrFail($id);
        $property->status = $request->status;
        $property->save();
        return response()->json(['success' => true], 200);
    }
}
