<?php

namespace App\Services;
use App\Http\Controllers\Controller;
use App\Traits\Res;
use Mpdf\Mpdf;

class GeneratePDFService extends Controller
{
    use Res;

    public function genPDF($data, $view = 'receipts.request_stock_points')
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'default_font' => 'cairo',
            'format' => 'A4',
        ]);
        $mpdf->SetDirectionality('rtl');
        $html = view("receipts.$view", $data)->render();
        $mpdf->WriteHTML($html);

        $name_with_ext = 'receipt_' . now()->format('Ymd_His') . '.pdf';
        $path = "storage/receipts/$name_with_ext";
        $mpdf->Output(public_path($path), 'F');
        // FOLDER PATH
        // $filePath = public_path($path);
        // return response($mpdf->Output('', 'S'))->header('Content-Type', 'application/pdf');
        // URL PATH
        $pdf_url = asset($path);
        return [
            'full_path' => $pdf_url,
            'path' => $path,
        ];
    }

}
