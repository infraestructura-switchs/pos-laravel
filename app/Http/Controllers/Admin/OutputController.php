<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Output;
use App\Traits\UtilityTrait;
use Illuminate\Http\Request;
use Mpdf\HTMLParserMode;
use Illuminate\Support\Facades\View;

class OutputController extends Controller {

    use UtilityTrait;

    public function show(Output $output) {

        $company = session('config');

        if ($company->type_bill === '0') {
            $pdf = $this->initMPdf();
            $pdf->setFooter('{PAGENO}');
            $pdf->SetHTMLFooter(View::make("pdf.output.footer"));
            $pdf->WriteHTML(View::make("pdf.output.template", compact('output', 'company')), HTMLParserMode::HTML_BODY);
        }else{
            $pdf = $this->initMPdfTicket(130);
            $pdf->setFooter('{PAGENO}');
            $pdf->SetHTMLFooter(View::make("pdf.output-ticket.footer"));
            $pdf->WriteHTML(View::make("pdf.output-ticket.template", compact('output', 'company')), HTMLParserMode::HTML_BODY);
        }

        return $pdf->Output('N° Pago-' . $output->id . '.pdf', 'I');
    }

    public function showWithWhatsapp(Output $output) {

        $company = session('config');

        if ($company->type_bill === '0') {
            $pdf = $this->initMPdf();
            $pdf->setFooter('{PAGENO}');
            $pdf->SetHTMLFooter(View::make("pdf.output.footer"));
            $pdf->WriteHTML(View::make("pdf.output.template", compact('output', 'company')), HTMLParserMode::HTML_BODY);
        }else{
            $pdf = $this->initMPdfTicket(130);
            $pdf->setFooter('{PAGENO}');
            $pdf->SetHTMLFooter(View::make("pdf.output-ticket.footer"));
            $pdf->WriteHTML(View::make("pdf.output-ticket.template", compact('output', 'company')), HTMLParserMode::HTML_BODY);
        }

        $fileName = 'Recibo_' . $output->id . '.pdf';

        // Solo subir a Cloudinary sin enviar por WhatsApp
        $result = $this->generateAndSendPdfViaWhatsapp($pdf, $fileName, null);

        if ($result['success']) {
            // Si se envió por WhatsApp, mostrar mensaje de éxito
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'file_url' => $result['file_url'] ?? null,
                'whatsapp_sent' => false // Los outputs no tienen cliente asociado
            ]);
        } else {
            // Si falló, mostrar el PDF normalmente
            return $pdf->Output('N° Pago-' . $output->id . '.pdf', 'I');
        }
    }

    /**
     * Sube únicamente el PDF del egreso a Cloudinary y retorna la URL.
     */
    public function uploadPdf(Output $output) {

        try {
            $company = session('config') ?? Company::first();

            if ($company->type_bill === '0') {
                $pdf = $this->initMPdf();
                $pdf->setFooter('{PAGENO}');
                $pdf->SetHTMLFooter(View::make("pdf.output.footer"));
                $pdf->WriteHTML(View::make("pdf.output.template", compact('output', 'company')), HTMLParserMode::HTML_BODY);
            }else{
                $pdf = $this->initMPdfTicket(130);
                $pdf->setFooter('{PAGENO}');
                $pdf->SetHTMLFooter(View::make("pdf.output-ticket.footer"));
                $pdf->WriteHTML(View::make("pdf.output-ticket.template", compact('output', 'company')), HTMLParserMode::HTML_BODY);
            }

            $fileName = 'Recibo_' . $output->id . '.pdf';

            $result = $this->generateAndSendPdfViaWhatsapp($pdf, $fileName, null);

            if (!($result['success'] ?? false)) {
                return response()->json($result, 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'PDF generado y subido correctamente',
                'file_url' => $result['file_url'] ?? null,
            ]);
        } catch (\Throwable $e) {
            \Log::error('uploadPdf Output error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function download(Output $output) {

        $company = session('config');

        if ($company->type_bill === '0') {
            $pdf = $this->initMPdf();
            $pdf->setFooter('{PAGENO}');
            $pdf->SetHTMLFooter(View::make("pdf.output.footer"));
            $pdf->WriteHTML(View::make("pdf.output.template", compact('output', 'company')), HTMLParserMode::HTML_BODY);
        }else{
            $pdf = $this->initMPdfTicket(130);
            $pdf->setFooter('{PAGENO}');
            $pdf->SetHTMLFooter(View::make("pdf.output-ticket.footer"));
            $pdf->WriteHTML(View::make("pdf.output-ticket.template", compact('output', 'company')), HTMLParserMode::HTML_BODY);
        }

        return $pdf->Output('N°-Recibo' . $output->id . '.pdf', 'D');
    }
}
