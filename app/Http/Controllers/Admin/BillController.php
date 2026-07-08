<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Company;
use App\Services\CompanyService;
use App\Services\WhatsappPdfService;
use App\Traits\UtilityTrait;
use Illuminate\Support\Facades\View;
use Mpdf\HTMLParserMode;
use Illuminate\Support\Facades\Log;

class BillController extends Controller
{
    use UtilityTrait;

    private function normalizeDataUriBase64(?string $value): ?string
    {
        if (!$value || !is_string($value)) {
            return $value;
        }

        if (!str_starts_with($value, 'data:image')) {
            return $value;
        }

        if (!str_contains($value, 'base64,')) {
            return $value;
        }

        [$meta, $payload] = explode('base64,', $value, 2);
        $payload = preg_replace('/\s+/', '', $payload ?? '');

        return $meta . 'base64,' . $payload;
    }

    private function shouldEmbedQrImage(?string $value): bool
    {
        if (!$value || !str_starts_with($value, 'data:image') || !str_contains($value, 'base64,')) {
            return false;
        }

        [, $payload] = explode('base64,', $value, 2);
        $payload = (string) ($payload ?? '');

        // Evita bloqueos de mPDF con imágenes embebidas demasiado grandes.
        if (strlen($payload) > 400000) {
            return false;
        }

        return base64_decode($payload, true) !== false;
    }

    private function sanitizeHtmlForPdf(string $html): string
    {
        $utf8Html = @iconv('UTF-8', 'UTF-8//IGNORE', $html);
        if ($utf8Html === false) {
            $utf8Html = $html;
        }

        return (string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $utf8Html);
    }

    private function prepareBillForPdf(Bill $bill): Bill
    {
        $bill->loadMissing([
            'details.product.unitMeasure',
            'details.documentTaxes.taxRates',
            'customer',
            'user',
            'paymentMethod',
            'documentTaxes.taxRates',
            'electronicBill',
            'finance',
            'numberingRange',
        ]);

        return $bill;
    }

    private function pdfDownloadResponse(\Mpdf\Mpdf $pdf, string $filename)
    {
        $content = $pdf->Output('', 'S');

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($content),
            'Cache-Control' => 'private, max-age=0, must-revalidate',
            'Pragma' => 'public',
        ]);
    }

    protected function buildPdfByType($company, Bill $bill, $range)
    {
        $dbTypeBillValue = Company::query()->value('type_bill');
        $dbTypeBill = (string) ($dbTypeBillValue ?? 'null');
        $typeBill = (string) ($company->type_bill ?? '1');

        if ($company instanceof Company && $company->id) {
            $persistedTypeBill = Company::query()->whereKey($company->id)->value('type_bill');
            if ($persistedTypeBill !== null) {
                $typeBill = (string) $persistedTypeBill;
            }
        }

        Log::info('BillController::buildPdfByType - Resolviendo formato de factura', [
            'bill_id' => $bill->id,
            'type_bill_session_or_company' => $typeBill,
            'type_bill_db' => $dbTypeBill,
            'is_electronic' => (bool) $bill->isElectronic,
            'has_electronic_bill' => (bool) $bill->electronicBill,
        ]);

        if ($typeBill === '0') {
            Log::info('BillController::buildPdfByType - Formato seleccionado: pdf.bill (normal)', [
                'bill_id' => $bill->id,
                'type_bill' => $typeBill,
            ]);
            $pdf = $this->initMPdf();
            $pdf->setFooter('{PAGENO}');
            $pdf->SetHTMLFooter(View::make('pdf.bill.footer', compact('company', 'bill', 'range')));
            $pdf->WriteHTML(View::make('pdf.bill.template', compact('company', 'bill', 'range')), HTMLParserMode::HTML_BODY);

            return $pdf;
        }

        if ($typeBill === '1') {
            Log::info('BillController::buildPdfByType - Formato seleccionado: pdf.ticket (ticket)', [
                'bill_id' => $bill->id,
                'type_bill' => $typeBill,
            ]);
            $height = $this->getHeigth($bill->details, $range);
            $pdf = $this->initMPdfTicket($height);
            $pdf->SetHTMLFooter(View::make('pdf.ticket.footer', compact('company', 'bill', 'range')));
            $pdf->WriteHTML(View::make('pdf.ticket.template', compact('company', 'bill', 'range')), HTMLParserMode::HTML_BODY);

            return $pdf;
        }

        Log::info('BillController::buildPdfByType - Formato seleccionado: pdf.bill-v2 (nuevo)', [
            'bill_id' => $bill->id,
            'type_bill' => $typeBill,
        ]);

        if ($bill->electronicBill && !empty($bill->electronicBill->qr_image)) {
            $originalQr = (string) $bill->electronicBill->qr_image;
            $normalizedQr = $this->normalizeDataUriBase64($originalQr);
            $canEmbedQr = $this->shouldEmbedQrImage($normalizedQr);
            $bill->electronicBill->qr_image = $canEmbedQr ? $normalizedQr : null;

            Log::info('BillController::buildPdfByType - QR normalizado para bill-v2', [
                'bill_id' => $bill->id,
                'is_data_uri' => str_starts_with($normalizedQr ?? '', 'data:image'),
                'qr_length' => strlen((string) $normalizedQr),
                'can_embed_qr' => $canEmbedQr,
            ]);

            if (!$canEmbedQr) {
                Log::warning('BillController::buildPdfByType - QR omitido para evitar bloqueo de render', [
                    'bill_id' => $bill->id,
                    'qr_length' => strlen((string) $normalizedQr),
                ]);
            }
        }

        try {
            $pdf = $this->initMPdf();
            $pdf->setFooter('{PAGENO}');

            Log::info('BillController::buildPdfByType - Render footer bill-v2 (inicio)', [
                'bill_id' => $bill->id,
            ]);

            $footerHtml = View::make('pdf.bill-v2.footer', compact('company', 'bill', 'range'))->render();
            $footerHtml = $this->sanitizeHtmlForPdf($footerHtml);
            $pdf->SetHTMLFooter($footerHtml);

            Log::info('BillController::buildPdfByType - Render footer bill-v2 (fin)', [
                'bill_id' => $bill->id,
                'footer_mode' => 'html',
                'footer_length' => strlen($footerHtml),
            ]);

            Log::info('BillController::buildPdfByType - Render template bill-v2 (inicio)', [
                'bill_id' => $bill->id,
            ]);

            $templateHtml = View::make('pdf.bill-v2.template', compact('company', 'bill', 'range'))->render();
            $templateHtml = $this->sanitizeHtmlForPdf($templateHtml);

            Log::info('BillController::buildPdfByType - Render template bill-v2 (html listo)', [
                'bill_id' => $bill->id,
                'template_length' => strlen($templateHtml),
            ]);

            $pdf->WriteHTML($templateHtml, HTMLParserMode::HTML_BODY);

            Log::info('BillController::buildPdfByType - Render template bill-v2 (fin)', [
                'bill_id' => $bill->id,
                'template_mode' => 'html',
                'template_length' => strlen($templateHtml),
            ]);

            return $pdf;
        } catch (\Throwable $e) {
            Log::error('BillController::buildPdfByType - Error renderizando bill-v2, aplicando fallback', [
                'bill_id' => $bill->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            $fallbackPdf = $this->initMPdf();
            $fallbackPdf->setFooter('{PAGENO}');
            $fallbackPdf->SetHTMLFooter(View::make('pdf.bill.footer', compact('company', 'bill', 'range')));
            $fallbackPdf->WriteHTML(View::make('pdf.bill.template', compact('company', 'bill', 'range')), HTMLParserMode::HTML_BODY);

            Log::warning('BillController::buildPdfByType - Fallback a pdf.bill aplicado', [
                'bill_id' => $bill->id,
            ]);

            return $fallbackPdf;
        }
    }

    public function sendWhatsapp(Bill $bill, WhatsappPdfService $service)
    {
        Log::info('Enviando factura por WhatsApp sendWhatsapp', ['bill_id' => $bill->id]);
        $result = $service->sendBillPdfViaWhatsapp($bill);
        if (!($result['success'] ?? false)) {
            return response()->json($result, 422);
        }
        return response()->json($result);
    }

    protected function createDPF(Bill $bill, $dest)
    {
        Log::info('Creando PDF de factura createDPF', ['bill_id' => $bill->id]);
        // Asegurar configuración de empresa aun si no está en sesión en esta ruta.
        // Debe ser un objeto/Modelo porque las vistas PDF acceden como propiedades.
        $company = session('config') ?? Company::first();

        $range = $bill->numberingRange;
        if (!$range) {
            // Rango ausente: crear objeto neutro para evitar errores en vistas/cálculos
            $range = (object) [
                'resolution_number' => null,
                'prefix' => '',
                'from' => '',
                'to' => '',
                'format_date_authorization' => null,
            ];
        }

        $pdf = $this->buildPdfByType($company, $bill, $range);

        $pdf->SetTitle('Factura '.$bill->number);

        return $pdf->Output('Factura '.$bill->number.'.pdf', $dest);
    }

    public function show(Bill $bill)
    {
        return $this->createDPF($bill, 'I');
    }

    public function showWithWhatsapp(Bill $bill)
    {
        Log::info('Mostrando factura con WhatsApp showWithWhatsapp', ['bill_id' => $bill->id]);
        $company = session('config') ?? Company::first();
        $customer = $bill->customer;
        $phone = $customer?->phone;

        $range = $bill->numberingRange;
        if (!$range) {
            $range = (object) [
                'resolution_number' => null,
                'prefix' => '',
                'from' => '',
                'to' => '',
                'format_date_authorization' => null,
            ];
        }

        $pdf = $this->buildPdfByType($company, $bill, $range);

        $pdf->SetTitle('Factura '.$bill->number);
        $fileName = 'Factura_' . ($bill->number ?? $bill->id) . '.pdf';

        // Generar, subir y enviar por WhatsApp automáticamente
        // TEMPORAL: Solo subir a Cloudinary sin enviar por WhatsApp
        $result = $this->generateAndSendPdfViaWhatsapp($pdf, $fileName, null);

        if ($result['success']) {
            // Si se envió por WhatsApp, mostrar mensaje de éxito
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'file_url' => $result['file_url'] ?? null,
                'whatsapp_sent' => !empty($phone)
            ]);
        } else {
            // Si falló, mostrar el PDF normalmente
            return $this->createDPF($bill, 'I');
        }
    }

    public function download(Bill $bill)
    {
        Log::info('Descargando factura download', ['bill_id' => $bill->id]);
        try {
            $bill = $this->prepareBillForPdf($bill);
            $company = session('config') ?? Company::first();
            $dbTypeBillValue = Company::query()->value('type_bill');
            $typeBill = (string) ($dbTypeBillValue ?? $company->type_bill ?? '1');

            Log::info('📥 BillController::download - Iniciando descarga', [
                'bill_id' => $bill->id,
                'is_electronic' => $bill->isElectronic,
                'type_bill' => $typeBill,
            ]);

            // Si es factura electrónica, usar el PDF completo con QR y CUFE
            if ($bill->isElectronic && $bill->electronicBill) {
                if ($typeBill !== '1') {
                    $range = $bill->numberingRange;
                    if (!$range) {
                        $range = (object) [
                            'resolution_number' => null,
                            'prefix' => '',
                            'from' => '',
                            'to' => '',
                            'format_date_authorization' => null,
                        ];
                    }

                    Log::info('⚡ BillController::download - Factura electrónica con type_bill no ticket, usando buildPdfByType', [
                        'bill_id' => $bill->id,
                        'type_bill' => $typeBill,
                    ]);

                    $pdf = $this->buildPdfByType($company, $bill, $range);
                    $pdf->SetTitle('Factura '.$bill->number);

                    Log::info('BillController::download - Enviando Output PDF electrónico no-ticket', [
                        'bill_id' => $bill->id,
                        'filename' => 'Factura-' . $bill->number . '.pdf',
                    ]);

                    return $this->pdfDownloadResponse($pdf, 'Factura-' . $bill->number . '.pdf');
                }

                Log::info('⚡ BillController::download - Descargando factura electrónica', ['bill_id' => $bill->id]);
                $pdfContent = base64_decode($this->getElectronicBillBase64($bill->id));
                return response($pdfContent, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="Factura-' . $bill->number . '.pdf"'
                ]);
            }

            // Ticket básico sin dependencias externas
            Log::info('📄 BillController::download - Descargando factura estándar', ['bill_id' => $bill->id]);
            $dbTypeBill = (string) ($dbTypeBillValue ?? 'null');

            Log::info('📄 BillController::download - Descargando factura estándar company ', ['company' => $company]);
            Log::info('BillController::download - Resolviendo flujo de descarga por type_bill', [
                'bill_id' => $bill->id,
                'type_bill_session_or_company' => $typeBill,
                'type_bill_db' => $dbTypeBill,
            ]);

            if ($typeBill !== '1') {
                $range = $bill->numberingRange;
                if (!$range) {
                    $range = (object) [
                        'resolution_number' => null,
                        'prefix' => '',
                        'from' => '',
                        'to' => '',
                        'format_date_authorization' => null,
                    ];
                }

                Log::info('BillController::download - Redirigiendo a buildPdfByType para formato no ticket', [
                    'bill_id' => $bill->id,
                    'type_bill' => $typeBill,
                ]);

                $pdf = $this->buildPdfByType($company, $bill, $range);
                $pdf->SetTitle('Factura '.$bill->number);

                Log::info('BillController::download - Enviando Output PDF estándar no-ticket', [
                    'bill_id' => $bill->id,
                    'filename' => 'Factura-' . $bill->id . '.pdf',
                ]);

                return $this->pdfDownloadResponse($pdf, 'Factura-' . $bill->id . '.pdf');
            }

            // Medidas del ticket: ancho en mm, alto dinámico aproximado
            $width = optional(session('config'))->width_ticket
                ?: (Company::query()->value('width_ticket') ?: 80);

            $itemsCount = $bill->details()->count();
            $height = 170 + max(0, ($itemsCount - 7)) * 8; // aproximación segura

            // Configurar directorio de caché temporal para evitar problemas de permisos
            $tempDir = storage_path('app/mpdf');
            if (!is_dir($tempDir)) {
                @mkdir($tempDir, 0775, true);
            }

            // Desactivar completamente el error handler de Laravel durante mPDF
            set_error_handler(function() { return true; });
            
            try {
                $pdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => [$width, $height],
                    'margin_left' => 3,
                    'margin_right' => 3,
                    'margin_top' => 6,
                    'margin_bottom' => 12,
                    'dpi' => 96,
                    'default_font' => 'dejavusans',
                    'tempDir' => $tempDir
                ]);
            } catch (\Throwable $e) {
                // Ignorar cualquier error durante la inicialización de mPDF
                Log::warning('Error ignorado durante inicialización de mPDF', ['error' => $e->getMessage()]);
                // Intentar crear sin tempDir personalizado
                $pdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => [$width, $height],
                    'margin_left' => 3,
                    'margin_right' => 3,
                    'margin_top' => 6,
                    'margin_bottom' => 12,
                    'dpi' => 96,
                    'default_font' => 'dejavusans'
                ]);
            } finally {
                restore_error_handler();
            }

            $billNum = $bill->number ?: $bill->id;
            $created = $bill->created_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i');

            // Construir HTML mínimo del ticket
            $html = '<div style="font-size:12px; font-family: DejaVu Sans, sans-serif; color:#1e293b;">';
            $html .= '<div style="text-align:center; margin-bottom:8px;">'
                   . '<div style="font-weight:700; font-size:22px;">' . e($company->name ?? 'Empresa') . '</div>'
                   . '<div style="font-size:11px;">' . e($company->nit ?? '') . '</div>'
                   . '<div style="font-size:11px;">' . e($company->direction ?? '') . '</div>'
                   . '<div style="font-size:11px;">' . e($company->phone ?? '') . '</div>'
                   . '</div>';

            $html .= '<hr />';

            $html .= '<table width="100%" style="font-size:12px;">'
                   . '<tr><td>Fecha</td><td style="text-align:right;">' . e($created) . '</td></tr>'
                   . '<tr><td>Cajero</td><td style="text-align:right;">' . e($bill->user?->name ?? '') . '</td></tr>'
                   . '<tr><td>Cliente</td><td style="text-align:right;">' . e($bill->customer?->names ?? 'Consumidor Final') . '</td></tr>'
                   . '<tr><td colspan="2" style="text-align:right; font-weight:700;">Venta: ' . e($billNum) . '</td></tr>'
                   . '</table>';

            $html .= '<hr />';

            // Items
            $html .= '<table width="100%" cellspacing="0" cellpadding="0" style="font-size:12px;">'
                   . '<thead><tr>'
                   . '<th style="text-align:left;">Producto o servicio</th>'
                   . '<th style="text-align:center; width:35px;">Cant</th>'
                   . '<th style="text-align:right; width:70px;">Total</th>'
                   . '</tr></thead><tbody>';
            foreach ($bill->details as $item) {
                $name = e($item->name);
                $qty = (int) $item->amount;
                $total = number_format((int) $item->total, 0, ',', '.');
                $html .= '<tr>'
                      . '<td style="text-align:left;">' . $name . '</td>'
                      . '<td style="text-align:center;">' . $qty . '</td>'
                      . '<td style="text-align:right;">$' . $total . '</td>'
                      . '</tr>';
            }
            $html .= '</tbody></table>';

            $html .= '<hr />';

            // Totales
            $subtotal = number_format((int) $bill->subtotal, 0, ',', '.');
            $tip = number_format((int) ($bill->tip ?? 0), 0, ',', '.');
            $discount = number_format((int) ($bill->discount ?? 0), 0, ',', '.');
            $tax = number_format((int) ($bill->tax ?? ($bill->documentTaxes->sum('tax_amount') ?? 0)), 0, ',', '.');
            $final = number_format((int) ($bill->final_total ?? $bill->total), 0, ',', '.');
            $cash = number_format((int) ($bill->cash ?? $bill->final_total ?? $bill->total), 0, ',', '.');
            $change = number_format((int) ($bill->change ?? 0), 0, ',', '.');

            $html .= '<table width="100%" style="font-size:12px;">'
                  . '<tr><td style="text-align:right;">Valor bruto:</td><td style="text-align:right; width:90px;">$' . $subtotal . '</td></tr>'
                  . '<tr><td style="text-align:right;">Servicio voluntario:</td><td style="text-align:right;">$' . $tip . '</td></tr>'
                  . '<tr><td style="text-align:right;">Descuento:</td><td style="text-align:right;">$' . $discount . '</td></tr>'
                  . '<tr><td style="text-align:right;">IVA</td><td style="text-align:right;">$' . $tax . '</td></tr>'
                  . '<tr><td style="text-align:right; font-weight:700;">Total a pagar:</td><td style="text-align:right; font-weight:700;">$' . $final . '</td></tr>'
                  . '</table>';

            $html .= '<hr />';

            $html .= '<table width="100%" style="font-size:12px;">'
                  . '<tr><td style="text-align:center; font-weight:700;">Forma de pago</td><td></td></tr>'
                  . '<tr><td style="text-align:right;">Efectivo:</td><td style="text-align:right;">$' . $cash . '</td></tr>'
                  . '<tr><td style="text-align:right;">Cambio:</td><td style="text-align:right;">$' . $change . '</td></tr>'
                  . '</table>';

            $html .= '<div style="text-align:center; margin-top:10px; font-size:11px;">'
                  . 'Elaborado por: SWICHTS 9999999<br/>www.switchs.co NIT: 901.740.642-1'
                  . '</div>';

            $html .= '</div>';

            $pdf->WriteHTML($html);
            return $this->pdfDownloadResponse($pdf, 'Factura-' . $bill->id . '.pdf');
        } catch (\Throwable $e) {
            Log::error('❌ BillController::download - Error', [
                'bill_id' => $bill->id ?? 'N/A',
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            return response('Error: ' . $e->getMessage(), 500, [
                'Content-Type' => 'text/plain; charset=UTF-8'
            ]);
        }
    }

    /**
     * Sube únicamente el PDF de la factura a Cloudinary y retorna la URL.
     */
    public function uploadPdf(Bill $bill)
    {
        try {
            $company = session('config') ?? Company::first();
            $range = $bill->numberingRange;
            if (!$range) {
                $range = (object) [
                    'resolution_number' => null,
                    'prefix' => '',
                    'from' => '',
                    'to' => '',
                    'format_date_authorization' => null,
                ];
            }

            $pdf = $this->buildPdfByType($company, $bill, $range);

            $pdf->SetTitle('Factura ' . $bill->number);
            $fileName = 'Factura_' . ($bill->number ?? $bill->id) . '.pdf';

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
            Log::error('uploadPdf Bill error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getBillBase64($bill_id)
    {
        $bill = Bill::find($bill_id);

        return base64_encode($this->createDPF($bill, 'S'));
    }

    /**
     * Genera el PDF ligero (mismo usado en Vender::download) y lo retorna en base64.
     */
    public function getDirectSaleBillBase64(int $billId): string
    {
        $bill = Bill::findOrFail($billId);

        // Construir ticket ligero como en download()
        $company = session('config') ?? Company::first();
        $width = optional(session('config'))->width_ticket ?: (Company::query()->value('width_ticket') ?: 80);
        $itemsCount = $bill->details()->count();
        $height = 170 + max(0, ($itemsCount - 7)) * 8;

        // Configurar directorio de caché temporal para evitar problemas de permisos
        $tempDir = storage_path('app/mpdf');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0775, true);
        }

        // Desactivar completamente el error handler de Laravel durante mPDF
        set_error_handler(function() { return true; });
        
        try {
            $pdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => [$width, $height],
                'margin_left' => 3,
                'margin_right' => 3,
                'margin_top' => 6,
                'margin_bottom' => 12,
                'dpi' => 96,
                'default_font' => 'dejavusans',
                'tempDir' => $tempDir
            ]);
        } catch (\Throwable $e) {
            Log::warning('Error ignorado durante inicialización de mPDF', ['error' => $e->getMessage()]);
            $pdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => [$width, $height],
                'margin_left' => 3,
                'margin_right' => 3,
                'margin_top' => 6,
                'margin_bottom' => 12,
                'dpi' => 96,
                'default_font' => 'dejavusans'
            ]);
        } finally {
            restore_error_handler();
        }

        $billNum = $bill->number ?: $bill->id;
        $created = $bill->created_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i');

        $html = '<div style="font-size:12px; font-family: DejaVu Sans, sans-serif; color:#1e293b;">';
        $html .= '<div style="text-align:center; margin-bottom:8px;">'
               . '<div style="font-weight:700; font-size:22px;">' . e($company->name ?? 'Empresa') . '</div>'
               . '<div style="font-size:11px;">' . e($company->nit ?? '') . '</div>'
               . '<div style="font-size:11px;">' . e($company->direction ?? '') . '</div>'
               . '<div style="font-size:11px;">' . e($company->phone ?? '') . '</div>'
               . '</div>';
        $html .= '<hr />';
        $html .= '<table width="100%" style="font-size:12px;">'
               . '<tr><td>Fecha</td><td style="text-align:right;">' . e($created) . '</td></tr>'
               . '<tr><td>Cajero</td><td style="text-align:right;">' . e($bill->user?->name ?? '') . '</td></tr>'
               . '<tr><td>Cliente</td><td style="text-align:right;">' . e($bill->customer?->names ?? 'Consumidor Final') . '</td></tr>'
               . '<tr><td colspan="2" style="text-align:right; font-weight:700;">Venta: ' . e($billNum) . '</td></tr>'
               . '</table>';
        $html .= '<hr />';
        $html .= '<table width="100%" cellspacing="0" cellpadding="0" style="font-size:12px;">'
               . '<thead><tr>'
               . '<th style="text-align:left;">Producto o servicio</th>'
               . '<th style="text-align:center; width:35px;">Cant</th>'
               . '<th style="text-align:right; width:70px;">Total</th>'
               . '</tr></thead><tbody>';
        foreach ($bill->details as $item) {
            $name = e($item->name);
            $qty = (int) $item->amount;
            $total = number_format((int) $item->total, 0, ',', '.');
            $html .= '<tr>'
                  . '<td style="text-align:left;">' . $name . '</td>'
                  . '<td style="text-align:center;">' . $qty . '</td>'
                  . '<td style="text-align:right;">$' . $total . '</td>'
                  . '</tr>';
        }
        $html .= '</tbody></table>';
        $html .= '<hr />';
        $subtotal = number_format((int) $bill->subtotal, 0, ',', '.');
        $tip = number_format((int) ($bill->tip ?? 0), 0, ',', '.');
        $discount = number_format((int) ($bill->discount ?? 0), 0, ',', '.');
        $tax = number_format((int) ($bill->tax ?? ($bill->documentTaxes->sum('tax_amount') ?? 0)), 0, ',', '.');
        $final = number_format((int) ($bill->final_total ?? $bill->total), 0, ',', '.');
        $cash = number_format((int) ($bill->cash ?? $bill->final_total ?? $bill->total), 0, ',', '.');
        $change = number_format((int) ($bill->change ?? 0), 0, ',', '.');
        $html .= '<table width="100%" style="font-size:12px;">'
              . '<tr><td style="text-align:right;">Valor bruto:</td><td style="text-align:right; width:90px;">$' . $subtotal . '</td></tr>'
              . '<tr><td style="text-align:right;">Servicio voluntario:</td><td style="text-align:right;">$' . $tip . '</td></tr>'
              . '<tr><td style="text-align:right;">Descuento:</td><td style="text-align:right;">$' . $discount . '</td></tr>'
              . '<tr><td style="text-align:right;">IVA</td><td style="text-align:right;">$' . $tax . '</td></tr>'
              . '<tr><td style="text-align:right; font-weight:700;">Total a pagar:</td><td style="text-align:right; font-weight:700;">$' . $final . '</td></tr>'
              . '</table>';
        $html .= '<hr />';
        $html .= '<table width="100%" style="font-size:12px;">'
              . '<tr><td style="text-align:center; font-weight:700;">Forma de pago</td><td></td></tr>'
              . '<tr><td style="text-align:right;">Efectivo:</td><td style="text-align:right;">$' . $cash . '</td></tr>'
              . '<tr><td style="text-align:right;">Cambio:</td><td style="text-align:right;">$' . $change . '</td></tr>'
              . '</table>';
        $html .= '<div style="text-align:center; margin-top:10px; font-size:11px;">Elaborado por: SWICHTS<br/>www.switchs.co NIT: 901.740.642-1</div>';
        $html .= '</div>';

        $pdf->WriteHTML($html);
        return base64_encode($pdf->Output('Factura-' . $bill->id . '.pdf', 'S'));
    }

    /**
     * Genera el PDF de factura electrónica con QR y CUFE en base64
     */
    public function getElectronicBillBase64(int $billId): string
    {
        Log::info('⚡ BillController::getElectronicBillBase64 - Iniciando', ['bill_id' => $billId]);
        
        $bill = Bill::findOrFail($billId);
        
        if (!$bill->electronicBill) {
            Log::warning('⚠️ BillController::getElectronicBillBase64 - No es factura electrónica, usando formato estándar');
            return $this->getDirectSaleBillBase64($billId);
        }

        $company = session('config') ?? Company::first();
        $electronicBill = $bill->electronicBill;
        Log::info('📋 BillController::getElectronicBillBase64 - Factura electrónica', [
            'bill_id' => $billId,
            'number' => $electronicBill->number,
            'has_qr' => !empty($electronicBill->qr_image),
            'has_cufe' => !empty($electronicBill->cufe),
            'company' => $company->only(['name', 'nit', 'direction', 'phone'])
        ]);
        
        // El accessor ya decodifica el JSON, no hacer json_decode nuevamente
        $numberingRange = $electronicBill->numbering_range;
        // Si viene como array, convertir a objeto para compatibilidad con el template
        if (is_array($numberingRange)) {
            $numberingRange = (object) $numberingRange;
        }
        
        Log::info('📋 BillController::getElectronicBillBase64 - Datos factura electrónica', [
            'bill_id' => $billId,
            'number' => $electronicBill->number,
            'has_qr' => !empty($electronicBill->qr_image),
            'has_cufe' => !empty($electronicBill->cufe)
        ]);

        // Dimensiones del ticket: más alto para incluir QR y CUFE
        $width = optional(session('config'))->width_ticket ?: (Company::query()->value('width_ticket') ?: 80);
        $itemsCount = $bill->details()->count();
        // Altura adicional para QR (aprox 50mm) + CUFE (aprox 30mm) + resolución (20mm)
        $height = 170 + max(0, ($itemsCount - 7)) * 8 + 100;

        // Configurar directorio de caché temporal para evitar problemas de permisos
        $tempDir = storage_path('app/mpdf');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0775, true);
        }

        // Desactivar completamente el error handler de Laravel durante mPDF
        set_error_handler(function() { return true; });
        
        try {
            $pdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => [$width, $height],
                'margin_left' => 3,
                'margin_right' => 3,
                'margin_top' => 6,
                'margin_bottom' => 12,
                'dpi' => 96,
                'default_font' => 'dejavusans',
                'tempDir' => $tempDir
            ]);
        } catch (\Throwable $e) {
            Log::warning('Error ignorado durante inicialización de mPDF', ['error' => $e->getMessage()]);
            $pdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => [$width, $height],
                'margin_left' => 3,
                'margin_right' => 3,
                'margin_top' => 6,
                'margin_bottom' => 12,
                'dpi' => 96,
                'default_font' => 'dejavusans'
            ]);
        } finally {
            restore_error_handler();
        }

        $billNum = $bill->number;
        $created = $bill->created_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i');

        // HTML del ticket con factura electrónica
        $html = '<div style="font-size:12px; font-family: DejaVu Sans, sans-serif; color:#1e293b;">';
        
        // Logo y empresa
        $html .= '<div style="text-align:center; margin-bottom:8px;">'
               . '<div style="font-weight:700; font-size:22px;">' . e($company->name ?? 'Empresa') . '</div>'
               . '<div style="font-size:11px;">NIT: ' . e($company->nit ?? '') . '</div>'
               . '<div style="font-size:11px;">Dirección: ' . e($company->direction ?? '') . '</div>'
               . '<div style="font-size:11px;">Celular: ' . e($company->phone ?? '') . '</div>'
               . '</div>';

        // Resolución DIAN
        if ($numberingRange && isset($numberingRange->resolution_number)) {
            $html .= '<div style="text-align:center; font-size:10px; margin-bottom:6px; line-height:1.3;">'
                   . 'Resolución DIAN <strong>' . e($numberingRange->resolution_number) . '</strong><br/>'
                   . 'Autorizada el <strong>' . e($numberingRange->start_date ?? '') . '</strong><br/>'
                   . 'Prefijo <strong>' . e($numberingRange->prefix) . '</strong> '
                   . 'del <strong>' . e($numberingRange->from) . '</strong> '
                   . 'al <strong>' . e($numberingRange->to) . '</strong><br/>'
                   . 'Vig ' . e($numberingRange->months ?? '') . ' meses'
                   . '</div>';
        }
        
        $html .= '<hr />';

        // Título factura electrónica
        $html .= '<div style="text-align:right; font-weight:700; font-size:13px; margin-bottom:6px;">'
               . 'Factura electrónica de venta: ' . e($billNum)
               . '</div>';

        // Información de la factura
        $html .= '<table width="100%" style="font-size:12px;">'
               . '<tr><td>Fecha</td><td style="text-align:right;">' . e($created) . '</td></tr>'
               . '<tr><td>Cajero</td><td style="text-align:right;">' . e($bill->user?->name ?? '') . '</td></tr>'
               . '<tr><td>C.C / NIT</td><td style="text-align:right;">' . e($bill->customer?->no_identification ?? '') . '</td></tr>'
               . '<tr><td>Cliente</td><td style="text-align:right;">' . e($bill->customer?->names ?? 'Consumidor Final') . '</td></tr>'
               . '</table>';
        
        $html .= '<hr />';

        // Productos
        $html .= '<table width="100%" cellspacing="0" cellpadding="0" style="font-size:12px;">'
               . '<thead><tr>'
               . '<th style="text-align:left;">Producto</th>'
               . '<th style="text-align:center; width:35px;">Cant</th>'
               . '<th style="text-align:right; width:70px;">Valor</th>'
               . '</tr></thead><tbody>';
        foreach ($bill->details as $item) {
            $name = e(\Illuminate\Support\Str::limit($item->name, 30));
            $qty = (int) $item->amount;
            $price = number_format((int) $item->price, 0, ',', '.');
            $html .= '<tr>'
                  . '<td style="text-align:left;">' . $name . '</td>'
                  . '<td style="text-align:center;">' . $qty . '</td>'
                  . '<td style="text-align:right;">$' . $price . '</td>'
                  . '</tr>';
        }
        $html .= '</tbody></table>';
        
        $html .= '<hr style="border-top: 2px dotted #000;" />';

        // Totales
        $subtotal = number_format((int) $bill->subtotal, 0, ',', '.');
        $tip = number_format((int) ($bill->tip ?? 0), 0, ',', '.');
        $discount = number_format((int) ($bill->discount ?? 0), 0, ',', '.');
        $final = number_format((int) ($bill->final_total ?? $bill->total), 0, ',', '.');
        
        $html .= '<table width="100%" style="font-size:12px; margin-top:4px;">'
              . '<tr><td style="text-align:right;">Subtotal:</td><td style="text-align:right; width:90px;">$' . $subtotal . '</td></tr>'
              . '<tr><td style="text-align:right;">Servicio voluntario:</td><td style="text-align:right;">$' . $tip . '</td></tr>'
              . '<tr><td style="text-align:right;">Descuento:</td><td style="text-align:right;">$' . $discount . '</td></tr>';
        
        // Impuestos
        foreach ($bill->documentTaxes as $tax) {
            $taxAmount = number_format((int) $tax->tax_amount, 0, ',', '.');
            $html .= '<tr><td style="text-align:right;">' . e($tax->tribute_name) . ':</td><td style="text-align:right;">$' . $taxAmount . '</td></tr>';
        }
        
        $html .= '<tr><td style="text-align:right; font-weight:700;">Total a pagar:</td><td style="text-align:right; font-weight:700;">$' . $final . '</td></tr>'
              . '</table>';
        
        $html .= '<hr />';

        // Forma de pago
        $cash = number_format((int) ($bill->cash ?? $bill->final_total ?? $bill->total), 0, ',', '.');
        $change = number_format((int) ($bill->change ?? 0), 0, ',', '.');
        
        $html .= '<table width="100%" style="font-size:12px;">'
              . '<tr><td style="text-align:center; font-weight:700;" colspan="2">Forma de pago</td></tr>'
              . '<tr><td style="text-align:right;">' . e($bill->paymentMethod->name ?? 'Efectivo') . ':</td><td style="text-align:right;">$' . $cash . '</td></tr>'
              . '<tr><td style="text-align:right;">Cambio:</td><td style="text-align:right;">$' . $change . '</td></tr>'
              . '</table>';

        // Código QR
        if (!empty($electronicBill->qr_image)) {
            $html .= '<div style="text-align:center; margin-top:8px;">'
                   . '<img src="' . e($electronicBill->qr_image) . '" style="width:140px; height:auto;" />'
                   . '</div>';
        }

        // CUFE
        if (!empty($electronicBill->cufe)) {
            $html .= '<div style="margin-top:6px;">'
                   . '<p style="text-align:center; font-weight:700; font-size:11px; margin-bottom:2px;">CUFE</p>'
                   . '<p style="word-wrap:break-word; font-size:8px; line-height:1.2;">' . e($electronicBill->cufe) . '</p>'
                   . '</div>';
        }

        // Footer
        $html .= '<div style="text-align:center; margin-top:10px; font-size:11px;">'
              . 'Elaborado por: ' . e($company->invoiceProvider->name ?? 'Empresa') . '<br/>' . e($company->invoiceProvider->url ?? 'Url') . ' NIT: ' . e($company->invoiceProvider->nit ?? 'Nit') . ''
              . '</div>';
        
        $html .= '</div>';

        $pdf->WriteHTML($html);
        
        Log::info('✅ BillController::getElectronicBillBase64 - PDF generado exitosamente', ['bill_id' => $billId]);
        
        return base64_encode($pdf->Output('Factura-' . $bill->number . '.pdf', 'S'));
    }

    /**
     * * Calcula el tamaño de la factura
     * ! Esta funcion solo sirve con las impresoras superiores a 80cm
     */
    protected function getHeigth($details, $range)
    {
        $oneLine = 0;
        $twoLines = 0;

        foreach ($details as $value) {
            if (strlen($value->name) > 27) {
                $twoLines++;
            } else {
                $oneLine++;
            }
        }

        $heightDefault = $range->resolution_number ? 190 : 170;

        $heightOneLine = $oneLine > 7 ? 4.5 : 8;

        return ((int) ($oneLine * $heightOneLine)) + ((int) ($twoLines * 7.5)) + $heightDefault;
    }

    /**
     * Este funcion devuelve la informacion de la factura para la impresion en el frontend
     */
    public function getBill(Bill $bill)
    {
        $customer = $bill->customer;
        $range = $bill->numberingRange;
        $products = $bill->details->transform(fn ($item) => $item->only(['name', 'amount', 'total']));
        $company = CompanyService::companyData();
        $dbTypeBillValue = Company::query()->value('type_bill');
        $typeBill = (string) ($dbTypeBillValue ?? optional(session('config'))->type_bill ?? '1');

        Log::info('BillController::getBill - Datos para impresion frontend', [
            'bill_id' => $bill->id,
            'type_bill' => $typeBill,
            'is_electronic' => (bool) $bill->isElectronic,
        ]);

        $data = [

            'is_electronic' => $bill->isElectronic,
            'company' => $company,
            'type_bill' => $typeBill,
            'customer' => [
                'identification' => $customer->no_identification,
                'names' => $customer->names,
            ],
            'bill' => [
                'cash' => $bill->cash,
                'change' => $bill->change,
                'format_created_at' => $bill->format_created_at,
                'discount' => $bill->discount,
                'tip' => $bill->tip,
                'number' => $bill->number,
                'subtotal' => $bill->subtotal,
                'total' => $bill->total,
                'final_total' => $bill->final_total,
                'user_name' => $bill->user->name,
                'payment_method' => $bill->paymentMethod->name,
            ],
            'products' => $products,
            'range' => [
                'prefix' => $range->prefix,
                'from' => $range->from,
                'to' => $range->to,
                'resolution_number' => $range->resolution_number,
                'date_authorization' => $range->format_date_authorization,
            ],
            'taxes' => $bill->documentTaxes->map(function ($item) {
                return [
                    'tribute_name' => $item->tribute_name,
                    'tax_amount' => $item->tax_amount,
                ];
            }),
        ];

        if ($bill->isElectronic) {
            $data['range'] = $bill->electronicBill->numbering_range;
            $data['electronic_bill'] = $bill->electronicBill->toArray();
        }

        return response()->json(['data' => $data]);
    }
}
