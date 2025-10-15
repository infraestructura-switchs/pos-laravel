<?php

namespace App\Jobs;

use App\Http\Controllers\Admin\BillController;
use App\Services\CloudinaryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadBillPdfToCloudinary implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $billId;

    public function __construct(int $billId)
    {
        $this->billId = $billId;
        $this->onQueue('default');
    }

    public function handle(): void
    {
        try {
            \Log::info('📤 Job UploadBillPdfToCloudinary - Iniciando', ['bill_id' => $this->billId]);
            @set_time_limit(300);
            @ini_set('memory_limit', '512M');

            /** @var BillController $billController */
            $billController = app(BillController::class);
            
            // Obtener la factura para verificar si es electrónica
            $bill = \App\Models\Bill::find($this->billId);
            
            if (!$bill) {
                \Log::error('❌ Job UploadBillPdfToCloudinary - Factura no encontrada', ['bill_id' => $this->billId]);
                return;
            }

            \Log::info('🔍 Job UploadBillPdfToCloudinary - Verificando tipo de factura', [
                'bill_id' => $this->billId,
                'is_electronic' => $bill->isElectronic,
                'has_electronic_bill' => $bill->electronicBill ? true : false
            ]);

            // Si es factura electrónica, usar el método que genera el PDF completo con QR y CUFE
            if ($bill->isElectronic && $bill->electronicBill) {
                \Log::info('⚡ Job UploadBillPdfToCloudinary - Generando PDF de factura electrónica', ['bill_id' => $this->billId]);
                $pdfBase64 = $billController->getElectronicBillBase64($this->billId);
            } else {
                \Log::info('📄 Job UploadBillPdfToCloudinary - Generando PDF estándar', ['bill_id' => $this->billId]);
                // Usar formato ligero (mismo de la vista Vender)
                $pdfBase64 = $billController->getDirectSaleBillBase64($this->billId);
            }
            
            \Log::info('✅ Job UploadBillPdfToCloudinary - PDF generado', [
                'bill_id' => $this->billId,
                'size_bytes' => strlen((string)$pdfBase64)
            ]);

            $tmpDir = storage_path('app/tmp');
            if (!is_dir($tmpDir)) {
                @mkdir($tmpDir, 0775, true);
            }

            $tmpName = 'bill_' . $this->billId . '_' . time() . '.pdf';
            $tmpPath = $tmpDir . DIRECTORY_SEPARATOR . $tmpName;
            @file_put_contents($tmpPath, base64_decode($pdfBase64));

            /** @var CloudinaryService $cloud */
            $cloud = app(CloudinaryService::class);
            \Log::info('☁️ Job UploadBillPdfToCloudinary - Subiendo a Cloudinary', ['bill_id' => $this->billId]);
            
            $upload = $cloud->uploadRaw($tmpPath, [
                'folder' => config('cloudinary.folder', 'pos-images') . '/pdfs',
                'public_id' => 'bill_' . $this->billId . '_' . time(),
                'resource_type' => 'raw',
            ]);
            
            \Log::info('✅ Job UploadBillPdfToCloudinary - PDF subido a Cloudinary', [
                'bill_id' => $this->billId,
                'success' => $upload['success'] ?? false,
                'url' => $upload['secure_url'] ?? $upload['url'] ?? 'N/A'
            ]);

            @unlink($tmpPath);

        } catch (\Throwable $e) {
            \Log::error('❌ Job UploadBillPdfToCloudinary - Error', [
                'bill_id' => $this->billId,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}


