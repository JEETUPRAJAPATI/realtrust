<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Yoeunes\Toastr\Facades\Toastr;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::orderBy('id', 'desc')->get();
        return view('admin.contact.index', compact('contacts'));
    }
    public function destroy(string $id)
    {
        $contact = Contact::find($id);
        $contact->delete();
        Toastr::success('message', 'Contact deleted successfully.');
        return back();
    }
    public function show($id)
    {
        $contact = Contact::where('id',$id)->firstOrFail();
        return view('admin.contact.show', compact('contact'));
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:resolved,pendding',
        ]);
        $property = Contact::findOrFail($id);
        $property->status = $request->status;
        $property->save();
        return response()->json(['success' => true], 200);
    }
}
