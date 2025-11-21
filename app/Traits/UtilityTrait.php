<?php

namespace App\Traits;

use App\Models\NumberingRange;
use App\Models\Company;
use App\Services\CloudinaryService;
use App\Services\WhatsappPdfService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;

trait UtilityTrait {

    public function initMPdf(): Mpdf {
        Log::info('Inicializando PDF initMPdf');
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        
        // Configurar directorio de caché temporal para evitar problemas de permisos
        $tempDir = storage_path('app/mpdf');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0775, true);
        }
        
        // Desactivar completamente el error handler de Laravel durante mPDF
        set_error_handler(function() { return true; });
        
        try {
            $pdf = new Mpdf([
                'mode' => 'utf-8',
                'fontDir' => array_merge($fontDirs, [
                    base_path('public/fonts/roboto/'),
                ]),
                'fontdata' => $fontData + [
                    'roboto' => [
                        'R' => 'Roboto-Regular.ttf',
                        'B' => 'Roboto-Bold.ttf',
                        'I' => 'Roboto-Italic.ttf',
                    ]
                ],
                'default_font' => 'roboto',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 5,
                'margin_bottom' => 5,
                'margin_header' => 5,
                'margin_footer' => 2,
                'tempDir' => $tempDir
            ]);
        } catch (\Throwable $e) {
            Log::warning('Error ignorado durante inicialización de mPDF', ['error' => $e->getMessage()]);
            $pdf = new Mpdf([
                'mode' => 'utf-8',
                'fontDir' => array_merge($fontDirs, [
                    base_path('public/fonts/roboto/'),
                ]),
                'fontdata' => $fontData + [
                    'roboto' => [
                        'R' => 'Roboto-Regular.ttf',
                        'B' => 'Roboto-Bold.ttf',
                        'I' => 'Roboto-Italic.ttf',
                    ]
                ],
                'default_font' => 'roboto',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 5,
                'margin_bottom' => 5,
                'margin_header' => 5,
                'margin_footer' => 2
            ]);
        } finally {
            restore_error_handler();
        }

        $cssPath = base_path('resources/views/pdf/styles.css');
        if (is_file($cssPath)) {
            $pdf->WriteHTML(file_get_contents($cssPath), HTMLParserMode::HEADER_CSS);
        }
        return $pdf;
    }

    protected function initMPdfTicket($height): Mpdf {
        Log::info('Inicializando PDF de ticket initMPdfTicket', ['height' => $height]);

        // Tomar ancho desde sesión; si no existe (ruta directa) usar Company o un valor por defecto
        $width = optional(session('config'))->width_ticket
            ?: (Company::query()->value('width_ticket') ?: 80);

        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        
        // Configurar directorio de caché temporal para evitar problemas de permisos
        $tempDir = storage_path('app/mpdf');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0775, true);
        }
        
        // Desactivar completamente el error handler de Laravel durante mPDF
        set_error_handler(function() { return true; });
        
        try {
            $pdf = new Mpdf([
                'mode' => 'utf-8',
                'fontDir' => array_merge($fontDirs, [
                    base_path('public/fonts/roboto/'),
                ]),
                'fontdata' => $fontData + [
                    'roboto' => [
                        'R' => 'Roboto-Regular.ttf',
                        'B' => 'Roboto-Bold.ttf',
                        'I' => 'Roboto-Italic.ttf',
                    ]
                ],
                'default_font' => 'roboto',
                'margin_left' => 3,
                'margin_right' => 3,
                'margin_top' => 10,
                'margin_bottom' => 20,
                'margin_header' => 3,
                'margin_footer' => 8,
                'format' => [$width, $height],
                'dpi' => 96,
                'tempDir' => $tempDir
            ]);
        } catch (\Throwable $e) {
            Log::warning('Error ignorado durante inicialización de mPDF', ['error' => $e->getMessage()]);
            $pdf = new Mpdf([
                'mode' => 'utf-8',
                'fontDir' => array_merge($fontDirs, [
                    base_path('public/fonts/roboto/'),
                ]),
                'fontdata' => $fontData + [
                    'roboto' => [
                        'R' => 'Roboto-Regular.ttf',
                        'B' => 'Roboto-Bold.ttf',
                        'I' => 'Roboto-Italic.ttf',
                    ]
                ],
                'default_font' => 'roboto',
                'margin_left' => 3,
                'margin_right' => 3,
                'margin_top' => 10,
                'margin_bottom' => 20,
                'margin_header' => 3,
                'margin_footer' => 8,
                'format' => [$width, $height],
                'dpi' => 96
            ]);
        } finally {
            restore_error_handler();
        }

        $cssPath = base_path('resources/views/pdf/styles.css');
        if (is_file($cssPath)) {
            $pdf->WriteHTML(file_get_contents($cssPath), HTMLParserMode::HEADER_CSS);
        }

        return $pdf;
    }

    /**
     * Genera PDF, lo guarda en storage/app/pdfs/, lo sube a Cloudinary y envía por WhatsApp automáticamente
     */
    protected function generateAndSendPdfViaWhatsapp($pdf, $fileName, $customerPhone = null, $deleteLocal = true)
    {
        try {
            // Crear directorio si no existe
            $pdfDir = storage_path('app/pdfs');
            if (!is_dir($pdfDir)) {
                @mkdir($pdfDir, 0775, true);
            }

            // Guardar PDF temporalmente
            $filePath = $pdfDir . DIRECTORY_SEPARATOR . $fileName;
            // Aumentar límites de ejecución y memoria para PDFs grandes
            @set_time_limit(180);
            @ini_set('memory_limit', '512M');
            $pdf->Output($filePath, 'F');

            // Subir a Cloudinary como raw
            $cloudinary = app(CloudinaryService::class);
            $upload = $cloudinary->uploadRaw($filePath, [
                'folder' => config('cloudinary.folder', 'pos-images') . '/pdfs',
                'public_id' => 'pdf_' . time() . '_' . uniqid(),
                'resource_type' => 'raw',
            ]);

            // Borrar archivo local si se solicita
            if ($deleteLocal && file_exists($filePath)) {
                @unlink($filePath);
            }

            if (!($upload['success'] ?? false)) {
                return [
                    'success' => false,
                    'message' => 'Error subiendo PDF: ' . ($upload['error'] ?? 'desconocido'),
                    'file_path' => $filePath
                ];
            }

            $fileUrl = $upload['secure_url'] ?? $upload['url'] ?? null;
            if (!$fileUrl) {
                return [
                    'success' => false,
                    'message' => 'No se obtuvo URL del archivo subido',
                    'file_path' => $filePath
                ];
            }

            // Si no hay teléfono, solo retornar la URL
            if (empty($customerPhone)) {
                return [
                    'success' => true,
                    'message' => 'PDF generado y subido',
                    'file_url' => $fileUrl,
                    'file_path' => $filePath
                ];
            }

            // Validar y normalizar teléfono
            $digits = preg_replace('/\D+/', '', $customerPhone);
            if (strlen($digits) < 10) {
                return [
                    'success' => false,
                    'message' => 'Número de teléfono inválido',
                    'file_url' => $fileUrl,
                    'file_path' => $filePath
                ];
            }

            // Enviar por WhatsApp vía N8N
            $endpoint = config('services.n8n.whatsapp_webhook_url');
            $payload = [
                'numberDestino' => (int) $digits,
                'fileName' => $fileUrl,
            ];

            Log::channel('whatsapp')->info('📤 UtilityTrait - Enviando a N8N', [
                'endpoint' => $endpoint,
                'payload' => $payload,
                'phone' => $digits
            ]);

            $response = Http::timeout((int) config('services.n8n.timeout', 10))
                ->acceptJson()
                ->asJson()
                ->post($endpoint, $payload);

            Log::channel('whatsapp')->info('📥 UtilityTrait - Respuesta de N8N', [
                'status' => $response->status(),
                'body' => $response->body(),
                'successful' => $response->successful(),
                'headers' => $response->headers()
            ]);

            if (!$response->successful()) {
                Log::channel('whatsapp')->warning('⚠️ N8N webhook error', [
                    'status' => $response->status(), 
                    'body' => $response->body(),
                    'endpoint' => $endpoint,
                    'payload' => $payload
                ]);
                return [
                    'success' => false,
                    'message' => 'Error enviando a N8N: ' . $response->status(),
                    'file_url' => $fileUrl,
                    'file_path' => $filePath
                ];
            }

            return [
                'success' => true,
                'message' => 'PDF generado, subido y enviado por WhatsApp',
                'file_url' => $fileUrl,
                'file_path' => $filePath,
                'n8n' => $response->json(),
            ];

        } catch (\Throwable $e) {
            Log::error('UtilityTrait generateAndSendPdfViaWhatsapp error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'file_path' => $filePath ?? null
            ];
        }
    }
}
