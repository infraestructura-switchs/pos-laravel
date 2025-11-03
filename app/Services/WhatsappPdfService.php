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
     * @param Bill $bill
     * @param string|null $customPhone Número de teléfono personalizado (opcional)
     */
    public function sendBillPdfViaWhatsapp(Bill $bill, ?string $customPhone = null): array
    {
        try {
            // Usar el número personalizado si se proporciona, sino usar el del cliente
            $phone = $customPhone ?? $bill->customer?->phone;
            
            if (empty($phone)) {
                return ['success' => false, 'message' => 'El cliente no tiene teléfono'];
            }
            // Normalizar a solo dígitos y validar longitud básica (>= 10)
            $digits = preg_replace('/\D+/', '', $phone);
            if (strlen($digits) < 10) {
                return ['success' => false, 'message' => 'Número de teléfono inválido'];
            }

            // Verificar si ya existe el PDF en Cloudinary (guardado en BD)
            $fileUrl = $bill->pdf_url;
            
            // Si no existe el PDF en BD, subirlo ahora
            if (empty($fileUrl)) {
                Log::info('📤 WhatsappPdfService - PDF no encontrado en BD, subiendo a Cloudinary', ['bill_id' => $bill->id]);
                
                // Generar PDF usando el método del BillController
                $billController = app(\App\Http\Controllers\Admin\BillController::class);
                
                // Si es factura electrónica, usar el PDF completo con QR y CUFE
                if ($bill->isElectronic && $bill->electronicBill) {
                    Log::info('⚡ WhatsappPdfService - Generando PDF con QR y CUFE', ['bill_id' => $bill->id]);
                    $pdfBase64 = $billController->getElectronicBillBase64($bill->id);
                } else {
                    Log::info('📄 WhatsappPdfService - Generando PDF estándar', ['bill_id' => $bill->id]);
                    $pdfBase64 = $billController->getDirectSaleBillBase64($bill->id);
                }
                
                $pdfContent = base64_decode($pdfBase64);

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
                
                // Guardar el URL en la base de datos para futuras referencias
                if ($fileUrl) {
                    $bill->pdf_url = $fileUrl;
                    $bill->save();
                    Log::info('💾 WhatsappPdfService - URL guardado en BD', ['bill_id' => $bill->id, 'pdf_url' => $fileUrl]);
                }
            } else {
                Log::info('✅ WhatsappPdfService - Usando PDF ya subido desde BD', [
                    'bill_id' => $bill->id,
                    'pdf_url' => $fileUrl
                ]);
            }
            
            if (!$fileUrl) {
                return ['success' => false, 'message' => 'No se obtuvo URL del archivo subido'];
            }

            // Llamar webhook N8N
            $endpoint = config('services.n8n.whatsapp_webhook_url');
            $payload = [
                'numberDestino' => (int) $digits,
                'fileName' => $fileUrl,
            ];

            Log::channel('whatsapp')->info('📤 WhatsappPdfService - Enviando a N8N', [
                'endpoint' => $endpoint,
                'payload' => $payload,
                'phone' => $digits,
                'bill_id' => $bill->id,
                'bill_number' => $bill->number
            ]);

            $response = Http::timeout((int) config('services.n8n.timeout', 10))
                ->acceptJson()
                ->asJson()
                ->post($endpoint, $payload);

            Log::channel('whatsapp')->info('📥 WhatsappPdfService - Respuesta de N8N', [
                'status' => $response->status(),
                'body' => $response->body(),
                'successful' => $response->successful(),
                'headers' => $response->headers(),
                'bill_id' => $bill->id
            ]);

            if (!$response->successful()) {
                Log::channel('whatsapp')->warning('⚠️ N8N webhook error', [
                    'status' => $response->status(), 
                    'body' => $response->body(),
                    'endpoint' => $endpoint,
                    'payload' => $payload,
                    'bill_id' => $bill->id
                ]);
                return ['success' => false, 'message' => 'Error enviando a N8N: ' . $response->status()];
            }

            return [
                'success' => true,
                'message' => 'Enviado por WhatsApp',
                'file_url' => $fileUrl,
                'n8n' => $response->json(),
            ];
        } catch (\Throwable $e) {
            Log::channel('whatsapp')->error('❌ WhatsappPdfService error: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'bill_id' => $bill->id ?? null
            ]);
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}


