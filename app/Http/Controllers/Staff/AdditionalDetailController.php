<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdditionalDetail;

class AdditionalDetailController extends Controller
{
   // Display list of additional details
    public function index()
    {
        $additionalDetails = AdditionalDetail::all();
        // dd($additionalDetails);
        return view('staff.additional-details.index', compact('additionalDetails'));
    }

    // Show form to create a new additional detail
    public function create()
    {
        return view('staff.additional-details.create');
    }

    // Store a new additional detail in the database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        AdditionalDetail::create([
            'name' => $request->name,
        ]);

        return redirect()->route('staff.additional-details.index')->with('success', 'Additional detail added successfully.');
    }

    // Show form to edit an existing additional detail
    public function edit($id)
    {
        $detail = AdditionalDetail::findOrFail($id);
        return view('staff.additional-details.edit', compact('detail'));
    }

    // Update an existing additional detail
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $detail = AdditionalDetail::findOrFail($id);
        $detail->update([
            'name' => $request->name,
        ]);

        return redirect()->route('staff.additional-details.index')->with('success', 'Additional detail updated successfully.');
    }

    // Delete an additional detail
    public function destroy($id)
    {
        $detail = AdditionalDetail::findOrFail($id);
        $detail->delete();

        return redirect()->route('staff.additional-details.index')->with('success', 'Additional detail deleted successfully.');
    }
}
