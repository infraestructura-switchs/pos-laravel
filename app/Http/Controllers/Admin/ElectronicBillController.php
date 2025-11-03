<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Services\Factus\ElectronicBillService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ElectronicBillController extends Controller
{
    /**
     * Descarga el PDF oficial de la factura electrónica desde Factus
     */
    public function downloadPdf(Bill $bill)
    {
        Log::info('📥 ElectronicBillController::downloadPdf - Descargando PDF', [
            'bill_id' => $bill->id,
            'user_id' => auth()->id()
        ]);

        if (!$bill->electronicBill || !$bill->electronicBill->is_validated) {
            Log::warning('⚠️ ElectronicBillController::downloadPdf - Factura no electrónica', [
                'bill_id' => $bill->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Esta factura no es una factura electrónica validada.'
            ], 400);
        }

        try {
            $pdfContent = ElectronicBillService::downloadPdf($bill);

            if (!$pdfContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo descargar el PDF de Factus. Intente nuevamente.'
                ], 500);
            }

            $fileName = "factura_electronica_{$bill->number}.pdf";

            Log::info('✅ ElectronicBillController::downloadPdf - PDF descargado exitosamente', [
                'bill_id' => $bill->id,
                'file_name' => $fileName
            ]);

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "attachment; filename=\"{$fileName}\"");

        } catch (\Exception $e) {
            Log::error('❌ ElectronicBillController::downloadPdf - Error', [
                'bill_id' => $bill->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al descargar el PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Descarga el XML oficial de la factura electrónica desde Factus
     */
    public function downloadXml(Bill $bill)
    {
        Log::info('📥 ElectronicBillController::downloadXml - Descargando XML', [
            'bill_id' => $bill->id,
            'user_id' => auth()->id()
        ]);

        if (!$bill->electronicBill || !$bill->electronicBill->is_validated) {
            Log::warning('⚠️ ElectronicBillController::downloadXml - Factura no electrónica', [
                'bill_id' => $bill->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Esta factura no es una factura electrónica validada.'
            ], 400);
        }

        try {
            $xmlContent = ElectronicBillService::downloadXml($bill);

            if (!$xmlContent) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo descargar el XML de Factus. Intente nuevamente.'
                ], 500);
            }

            $fileName = "factura_electronica_{$bill->number}.xml";

            Log::info('✅ ElectronicBillController::downloadXml - XML descargado exitosamente', [
                'bill_id' => $bill->id,
                'file_name' => $fileName
            ]);

            return response($xmlContent)
                ->header('Content-Type', 'application/xml')
                ->header('Content-Disposition', "attachment; filename=\"{$fileName}\"");

        } catch (\Exception $e) {
            Log::error('❌ ElectronicBillController::downloadXml - Error', [
                'bill_id' => $bill->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al descargar el XML: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra la información de la factura electrónica
     */
    public function show(Bill $bill)
    {
        if (!$bill->electronicBill) {
            return response()->json([
                'success' => false,
                'message' => 'Esta factura no es una factura electrónica.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'bill_id' => $bill->id,
                'number' => $bill->electronicBill->number,
                'cufe' => $bill->electronicBill->cufe,
                'is_validated' => $bill->electronicBill->is_validated,
                'qr_image' => $bill->electronicBill->qr_image,
                'created_at' => $bill->electronicBill->created_at,
                'numbering_range' => $bill->electronicBill->numbering_range,
            ]
        ]);
    }
}

