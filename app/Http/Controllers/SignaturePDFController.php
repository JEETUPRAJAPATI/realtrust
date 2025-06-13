<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SignaturePDFController extends Controller
{
    public function index()
    {
        return view('signaturePDF');
    }

    public function upload(Request $request)
    {
        $data['image'] = $this->uploadSignature($request->signed);
        $pdf = Pdf::loadView('signaturePDFView', $data);
        return $pdf->download('signature.pdf');
    }
    public function uploadSignature($signed)
    {

        $folderPath = public_path('upload/');
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $image_parts = explode(";base64,", $signed);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $file = $folderPath . uniqid() . '.' . $image_type;
        file_put_contents($file, $image_base64);
        return $file;
    }
}
