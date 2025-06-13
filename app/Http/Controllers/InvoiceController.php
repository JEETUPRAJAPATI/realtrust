<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paymentes;
use App\Models\Property;
use App\Models\User;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;


class InvoiceController extends Controller
{
    public function invoicesList (Request $request) {
        $query = Invoice::query();

        // Filter by mobile number if provided
        if ($request->has('mobile_no') && $request->mobile_no != '') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('mobile_no', 'like', '%' . $request->mobile_no . '%');
            });
        }
            // Filter by payment type if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('payment_type', $request->status);
        }
    
        $invoices = $query->with('user')->get();
    
        return view('admin.payments.invoice', compact('invoices'));
    }
    public function downloadInvoice($id)
    {
        // Fetch the invoice data by ID
        $invoice = Invoice::with( 'user')->findOrFail($id);
      
        $property =  Property::where('unique_id', $invoice->property_id)->first();
        // return view('admin.payments.invoice_pdf', compact('invoice','property'));

        // Pass the data to a Blade view for generating the PDF
        $pdf = PDF::loadView('admin.payments.invoice_pdf', compact('invoice','property'));

        // Return the PDF as a downloadable file
        return $pdf->download('invoice-' . $invoice->id . '.pdf');
    }
    public function create () {
        $properties = Property::where('status','Active')->get();
        $users = User::get();
        return view('admin.payments.create', compact('properties','users'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'user' => 'required|string',
            'user_add' => 'nullable|string',
            'property' => 'required|string',
            'seller_add' => 'nullable|string',
            'invoice_date' => 'required|date',
            'gst_percent' => 'required|numeric',
            'total_amount' => 'required|numeric',
            'payment_mode' => 'required'
        ]);
        $gstAmount = ($request->total_amount * $request->gst_percent) / 100;
        $totalAmount = $request->total_amount + $gstAmount;
        $invoice =  Invoice::create([
            'user_id' => $request->user,
            'order_id' => 'REL-' . strtoupper(uniqid()),
            'user_add' => $request->user_add,
            'property_id' => $request->property,
            'seller_add' => $request->seller_add,
            'invoice_date' => $request->invoice_date,
            'amount' => $request->total_amount,
            'gst_percent' => $request->gst_percent,
            'total_amount' => $totalAmount,
            'payment_mode' => $request->payment_mode,
            'payment_type' => 'invoice',
            'status' => $request->status
        ]);
        $property = Property::where('unique_id', $invoice->property_id)->firstOrFail();
        
        // Send the invoice email
        Mail::to($property->owner->email)->send(new InvoiceMail($invoice, $property));
        return redirect()->route('admin.invoice.list')->with('success', 'Invoice saved successfully!');
    }
    public function edit (Request $request, $id) {
        $invoice = Invoice::findOrFail($id);
        $properties = Property::get();
        $users = User::get();
        return view('admin.payments.edit', compact('properties','users','invoice'));
    }
    public function update(Request $request, $id)
    {
    // Validate the incoming request
    $request->validate([
        'user' => 'nullable|string',
        'user_add' => 'nullable|string',
        'property' => 'nullable|string',
        'seller_add' => 'nullable|string',
        'invoice_date' => 'required|date',
        'gst_percent' => 'required|numeric',
        'total_amount' => 'required|numeric',
        'payment_mode' => 'required'
    ]);

    // Find the invoice by ID
    $invoice = Invoice::findOrFail($id);

    // Calculate GST and total amount
    $gstAmount = ($request->total_amount * $request->gst_percent) / 100;
    $totalAmount = $request->total_amount + $gstAmount;

    // Update the invoice record
    $invoice->update([
        'user_id' => $invoice->user_id,
        'order_id' => $invoice->order_id,  // Keep the same order ID
        'user_add' => $request->user_add,
        'property_id' => $invoice->property_id,
        'seller_add' => $request->seller_add,
        'invoice_date' => $request->invoice_date,
        'amount' => $request->total_amount,
        'gst_percent' => $request->gst_percent,
        'total_amount' => $totalAmount,
        'payment_mode' => $request->payment_mode,
        'status' => $request->status
    ]);

    return redirect()->route('admin.invoice.list')->with('success', 'Invoice updated successfully!');
}


}
