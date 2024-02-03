<?php
namespace App\Traits\Api\V1;

use Illuminate\Support\Facades\Storage;

trait PDFs
{

    // This function changes the name of uploaded PDF files
    public static function givePDFRandomName($pdf)
    {
        return uniqid() . '.' . $pdf->getClientOriginalExtension();
    }

    // This function deletes a PDF file
    // public static function deletePDF($pdf, string $path) {
    //     Storage::delete($path . $pdf);
    // }
    public static function deletePDF($pdf, string $path)
    {
        $pdfPath = public_path($path . $pdf);
        if (file_exists($pdfPath)) {
            unlink($pdfPath);
        }
    }

    // This function stores PDF files
    // public static function storePDF($pdf, string $pdfName, string $path) {
    //     $pdf->storeAs($path, $pdfName);
    // } 
    public static function storePDF($pdf, string $pdfName, string $path)
    {
        $pdf->move(public_path($path), $pdfName);
    }
}
