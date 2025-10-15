<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryRemission;
use App\Traits\UtilityTrait;
use Illuminate\Support\Facades\View;
use Mpdf\HTMLParserMode;

class RemissionController extends Controller
{
    use UtilityTrait;

    public function show($id)
    {
        $remission = InventoryRemission::with(['warehouse', 'user', 'remissionDetails.product'])->findOrFail($id);
        $mpdf = $this->initMPdf();
        $mpdf->SetHTMLHeader(View::make('pdf.remission-detail.header'));
        $mpdf->SetHTMLFooter(View::make('pdf.remission-detail.footer'));
        $mpdf->WriteHTML(View::make('pdf.remission-detail.template', compact('remission')), HTMLParserMode::HTML_BODY);
        return $mpdf->Output('Remisión-' . $remission->folio . '.pdf', 'I'); // 'I' para mostrar en el navegador
    }

    public function download($id)
    {
        $remission = InventoryRemission::with(['warehouse', 'user', 'remissionDetails.product'])->findOrFail($id);
        $mpdf = $this->initMPdf();
        $mpdf->SetHTMLHeader(View::make('pdf.remission-detail.header'));
        $mpdf->SetHTMLFooter(View::make('pdf.remission-detail.footer'));
        $mpdf->WriteHTML(View::make('pdf.remission-detail.template', compact('remission')), HTMLParserMode::HTML_BODY);
        return $mpdf->Output('Remisión-' . $remission->folio . '.pdf', 'D'); // 'D' para descargar directamente
    }
}