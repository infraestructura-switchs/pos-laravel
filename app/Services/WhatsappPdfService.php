<?php

namespace App\Services;

use App\Models\Bill;
use App\Traits\UtilityTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Mpdf\HTMLParserMode;

class WhatsappPdfService
{
    use UtilityTrait;

    private CloudinaryService $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    /**
     * Genera el PDF de la factura, lo sube a Cloudinary como raw y llama al webhook de N8N.
     * Retorna array con success, message y opcionalmente urls.
     */
    public function sendBillPdfViaWhatsapp(Bill $bill): array
    {
        try {
            $customer = $bill->customer;
            $phone = $customer?->phone;
            if (empty($phone)) {
                return ['success' => false, 'message' => 'El cliente no tiene teléfono'];
            }
            // Normalizar a solo dígitos y validar longitud básica (>= 10)
            $digits = preg_replace('/\D+/', '', $phone);
            if (strlen($digits) < 10) {
                return ['success' => false, 'message' => 'Número de teléfono inválido'];
            }

            // Generar PDF usando el método del BillController
            $billController = app(\App\Http\Controllers\Admin\BillController::class);
            $pdfContent = base64_decode($billController->getBillBase64($bill->id));

            $tmpPath = storage_path('app/tmp');
            if (!is_dir($tmpPath)) {
                @mkdir($tmpPath, 0775, true);
            }
            $fileName = 'Factura_' . ($bill->number ?? $bill->id) . '.pdf';
            $filePath = $tmpPath . DIRECTORY_SEPARATOR . $fileName;
            file_put_contents($filePath, $pdfContent);

            // Subir a Cloudinary como raw
            $upload = $this->cloudinary->uploadRaw($filePath, [
                'folder' => config('cloudinary.folder', 'pos-images') . '/bills',
                'public_id' => 'bill_' . ($bill->number ?? $bill->id) . '_' . time(),
                'resource_type' => 'raw',
            ]);

            @unlink($filePath);

            if (!($upload['success'] ?? false)) {
                return ['success' => false, 'message' => 'Error subiendo PDF: ' . ($upload['error'] ?? 'desconocido')];
            }

            $fileUrl = $upload['secure_url'] ?? $upload['url'] ?? null;
            if (!$fileUrl) {
                return ['success' => false, 'message' => 'No se obtuvo URL del archivo subido'];
            }

            // Llamar webhook N8N
            $endpoint = config('services.n8n.whatsapp_webhook_url');
            $payload = [
                'numberDestino' => (int) $digits,
                'Fileurl' => $fileUrl,
            ];

            $response = Http::timeout((int) config('services.n8n.timeout', 10))
                ->acceptJson()
                ->asJson()
                ->post($endpoint, $payload);

            if (!$response->successful()) {
                Log::warning('N8N webhook error', ['status' => $response->status(), 'body' => $response->body()]);
                return ['success' => false, 'message' => 'Error enviando a N8N: ' . $response->status()];
            }

            return [
                'success' => true,
                'message' => 'Enviado por WhatsApp',
                'file_url' => $fileUrl,
                'n8n' => $response->json(),
            ];
        } catch (\Throwable $e) {
            Log::error('WhatsappPdfService error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}


