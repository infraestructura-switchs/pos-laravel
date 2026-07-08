<?php

namespace App\Services\Factus;

use App\Enums\LegalOrganization;
use App\Exceptions\CustomException;
use App\Models\Bill;
use App\Models\Tribute;
use App\Services\CompanyService;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class ElectronicBillService
{
    /**
     * Valida y envía la factura a la API de Factus
     */
    public static function validate(Bill $bill): Response
    {
        Log::info('📤 ElectronicBillService::validate - Preparando datos para Factus', [
            'bill_id' => $bill->id,
            'bill_number' => $bill->number
        ]);

        $data = self::prepareData($bill);
        
        Log::info('📡 ElectronicBillService::validate - Enviando factura a Factus', [
            'bill_id' => $bill->id,
            'items_count' => count($data['items']),
            'customer_id' => $data['customer']['identification']
        ]);

        $response = HttpService::apiHttp()
            ->post('bills/validate', $data);

        Log::info('✅ ElectronicBillService::validate - Respuesta recibida de Factus', [
            'bill_id' => $bill->id,
            'status' => $response->status()
        ]);

        return $response;
    }

    private static function prepareData(Bill $bill): array
    {
        Log::info('🔧 ElectronicBillService::prepareData - Iniciando preparación de datos', [
            'bill_id' => $bill->id
        ]);

        // Validar que la factura tenga detalles
        $details = $bill->details;
        if ($details->isEmpty()) {
            throw new CustomException('La factura no tiene productos para enviar a Factus');
        }

        $items = [];

        foreach ($details as $detail) {
            // Validar que el detalle tenga los impuestos configurados
            $documentTax = $detail->documentTaxes->first();
            if (!$documentTax) {
                throw new CustomException("El producto '{$detail->name}' no tiene impuestos configurados");
            }

            $taxRate = $documentTax->taxRates->first();
            if (!$taxRate) {
                throw new CustomException("El producto '{$detail->name}' no tiene tasa de impuesto configurada");
            }

            // Obtener el ID del tributo en la API de Factus
            $tribute = Tribute::where('name', $documentTax->tribute_name)->first();
            if (!$tribute || !$tribute->api_tribute_id) {
                throw new CustomException("El tributo '{$documentTax->tribute_name}' no está mapeado a la API de Factus");
            }

            // Validar que el producto tenga referencia
            if (!$detail->product->reference) {
                throw new CustomException("El producto '{$detail->name}' no tiene código de referencia configurado");
            }

            $unitMeasureCode = trim((string) (optional($detail->product->unitMeasure)->code ?? ''));

            $items[] = [
                'code_reference' => $detail->product->reference,
                'name' => $detail->name,
                'quantity' => $detail->amount,
                'discount_rate' => calculateDiscountPercentage($detail->price, (int) $detail->amount, $detail->discount),
                'discount' => $detail->discount,
                'price' => $detail->price,
                'tax_rate' => $taxRate->rate,
                'withholding_taxes' => [],
                'is_excluded' => $detail->product->taxRates->first()->id === 1 ? 1 : 0,
                'unit_measure_id' => $unitMeasureCode !== '' ? $unitMeasureCode : 70,
                //'unit_measure_id' => 70, // Unidad
                'standard_code_id' => 1, // Estándar de adopción del contribuyente
                'tribute_id' => $tribute->api_tribute_id,
            ];
        }

        Log::info('✅ ElectronicBillService::prepareData - Items preparados', [
            'items_count' => count($items)
        ]);

        // Validar datos del cliente
        $customer = $bill->customer;
        if (!$customer->identification_document_id) {
            throw new CustomException('El cliente no tiene tipo de documento configurado');
        }
        if (!$customer->no_identification) {
            throw new CustomException('El cliente no tiene número de identificación');
        }
        if (!$customer->email) {
            throw new CustomException('El cliente no tiene email configurado. El email es obligatorio para facturación electrónica');
        }
        if (!$customer->phone) {
            throw new CustomException('El cliente no tiene teléfono configurado. El teléfono es obligatorio para facturación electrónica');
        }

        $customerData = [
            'identification_document_id' => $customer->identification_document_id,
            'identification' => $customer->no_identification,
            'tribute_id' => $customer->tribute,
            'legal_organization_id' => $customer->legal_organization,
            'dv' => $customer->dv,
            'names' => $customer->legal_organization == LegalOrganization::NATURAL_PERSON->value ? $customer->names : null,
            'company' => $customer->legal_organization == LegalOrganization::LEGAL_PERSON->value ? $customer->names : null,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'address' => $customer->direction ?? 'N/A',
        ];

        Log::info('✅ ElectronicBillService::prepareData - Datos del cliente preparados', [
            'customer_id' => $customer->id,
            'customer_name' => $customer->names
        ]);

        // Validar método de pago
        if (!$bill->paymentMethod || !$bill->paymentMethod->code) {
            throw new CustomException('La factura no tiene método de pago configurado');
        }

        $data = [
            'reference_code' => $bill->reference_code ?? 'REF-' . $bill->id,
            'payment_method_code' => $bill->paymentMethod->code,
            'customer' => $customerData,
            'items' => $items,
            'observation' => $bill->observation ?? '',
        ];

        // Obtener el ID del rango de numeración de Factus desde el terminal
        $terminal = auth()->user()->terminals->first();
        if ($terminal && $terminal->factus_numbering_range_id) {
            $data['numbering_range_id'] = $terminal->factus_numbering_range_id;
            Log::info('📋 ElectronicBillService::prepareData - Rango de numeración de Factus configurado', [
                'numbering_range_id' => $terminal->factus_numbering_range_id
            ]);
        } else {
            Log::warning('⚠️ ElectronicBillService::prepareData - No hay rango de numeración de Factus configurado en el terminal');
        }

        Log::info('✅ ElectronicBillService::prepareData - Datos preparados exitosamente');

        return $data;
    }

    /**
     * Guarda los datos de la factura validada en la base de datos
     */
    public static function saveElectronicBill(array $responseData, Bill $bill): void
    {
        Log::info('💾 ElectronicBillService::saveElectronicBill - Guardando factura electrónica', [
            'bill_id' => $bill->id
        ]);

        // Validar que la respuesta tenga la estructura esperada
        if (!isset($responseData['data']['bill'])) {
            Log::error('❌ ElectronicBillService::saveElectronicBill - Respuesta inválida', [
                'response' => $responseData
            ]);
            throw new CustomException('La respuesta de Factus no contiene los datos de la factura');
        }

        $electronicBillData = $responseData['data']['bill'];
        $numberingRange = $responseData['data']['numbering_range'] ?? null;

        // Validar que tenga los campos necesarios
        if (!isset($electronicBillData['number'])) {
            throw new CustomException('La respuesta de Factus no contiene el número de factura');
        }
        if (!isset($electronicBillData['cufe'])) {
            throw new CustomException('La respuesta de Factus no contiene el CUFE');
        }

        // Actualizar el número de la factura en la tabla bills
        $bill->number = $electronicBillData['number'];
        $bill->save();

        Log::info('📝 ElectronicBillService::saveElectronicBill - Número de factura actualizado', [
            'bill_id' => $bill->id,
            'bill_number' => $electronicBillData['number']
        ]);

        $qrImage = $electronicBillData['qr_image'] ?? null;
        if (is_string($qrImage) && str_starts_with($qrImage, 'data:image') && str_contains($qrImage, 'base64,')) {
            [$meta, $payload] = explode('base64,', $qrImage, 2);
            $payload = preg_replace('/\s+/', '', $payload ?? '');
            $qrImage = $meta . 'base64,' . $payload;
        }

        // Preparar datos para guardar
        $dataToSave = [
            'number' => $electronicBillData['number'],
            'qr_image' => $qrImage,
            'cufe' => $electronicBillData['cufe'],
            'numbering_range' => $numberingRange ? json_encode($numberingRange) : null,
            'is_validated' => true,
        ];

        Log::info('Guardando datos electrónicos FACTUS', [
            'qr_image' => $electronicBillData['qr_image'],
            'cufe' => $electronicBillData['cufe'],
            'response_keys' => array_keys($electronicBillData),
            'billNumber' => $electronicBillData['number'],
            'dataToSave' => $dataToSave,
        ]);

        // Crear o actualizar el registro de factura electrónica
        if (!$bill->electronicBill) {
            $bill->electronicBill()->create($dataToSave);
            Log::info('✅ ElectronicBillService::saveElectronicBill - Factura electrónica creada', [
                'bill_id' => $bill->id,
                'cufe' => $electronicBillData['cufe']
            ]);
        } else {
            $bill->electronicBill()->update($dataToSave);
            Log::info('✅ ElectronicBillService::saveElectronicBill - Factura electrónica actualizada', [
                'bill_id' => $bill->id,
                'cufe' => $electronicBillData['cufe']
            ]);
        }
    }

    /**
     * Guarda la nota credito en la API
     */
    public static function storeCreditNote(Bill $bill): void
    {
        $response = HttpService::apiHttp()
            ->post('credit-notes/store-using-bill', ['bill_number' => $bill->number])
            ->json();

        $bill->electronicCreditNote()->create([
            'number' => $response['data']['credit_note']['number'],
        ]);
    }

    /**
     * Envia la nota credito a la DIAN
     */
    public static function validateCreditNote(Bill $bill): Response
    {
        $bill = $bill->fresh();
        $response = HttpService::apiHttp()->post('credit-notes/send/'.$bill->electronicCreditNote->number);

        return $response;
    }

    /**
     * Guarda los datos de la nota credito validada en la base de datos
     */
    public static function saveCreditNote(array $data, Bill $bill): void
    {
        $electronicCreditNote = $data['data']['credit_note'];

        $bill->electronicCreditNote()->update([
            'number' => $electronicCreditNote['number'],
            'qr_image' => $electronicCreditNote['qr_image'],
            'cude' => $electronicCreditNote['cude'],
            'is_validated' => true,
        ]);
    }

    /**
     * Obtiene el PDF oficial de la factura electrónica desde Factus
     */
    public static function downloadPdf(Bill $bill): ?string
    {
        if (!$bill->electronicBill || !$bill->electronicBill->is_validated) {
            Log::warning('⚠️ ElectronicBillService::downloadPdf - Factura no validada', [
                'bill_id' => $bill->id
            ]);
            return null;
        }

        try {
            Log::info('📄 ElectronicBillService::downloadPdf - Descargando PDF de Factus', [
                'bill_id' => $bill->id,
                'bill_number' => $bill->number
            ]);

            $response = HttpService::apiHttp()
                ->get("bills/{$bill->number}/pdf");

            if ($response->successful()) {
                Log::info('✅ ElectronicBillService::downloadPdf - PDF descargado exitosamente', [
                    'bill_id' => $bill->id
                ]);
                return $response->body();
            }

            Log::error('❌ ElectronicBillService::downloadPdf - Error al descargar PDF', [
                'bill_id' => $bill->id,
                'status' => $response->status()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('❌ ElectronicBillService::downloadPdf - Excepción', [
                'bill_id' => $bill->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Obtiene el XML oficial de la factura electrónica desde Factus
     */
    public static function downloadXml(Bill $bill): ?string
    {
        if (!$bill->electronicBill || !$bill->electronicBill->is_validated) {
            Log::warning('⚠️ ElectronicBillService::downloadXml - Factura no validada', [
                'bill_id' => $bill->id
            ]);
            return null;
        }

        try {
            Log::info('📄 ElectronicBillService::downloadXml - Descargando XML de Factus', [
                'bill_id' => $bill->id,
                'bill_number' => $bill->number
            ]);

            $response = HttpService::apiHttp()
                ->get("bills/{$bill->number}/xml");

            if ($response->successful()) {
                Log::info('✅ ElectronicBillService::downloadXml - XML descargado exitosamente', [
                    'bill_id' => $bill->id
                ]);
                return $response->body();
            }

            Log::error('❌ ElectronicBillService::downloadXml - Error al descargar XML', [
                'bill_id' => $bill->id,
                'status' => $response->status()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('❌ ElectronicBillService::downloadXml - Excepción', [
                'bill_id' => $bill->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Obtiene la URL del PDF oficial de la factura electrónica
     */
    public static function getPdfUrl(Bill $bill): ?string
    {
        if (!$bill->electronicBill || !$bill->electronicBill->is_validated) {
            return null;
        }

        try {
            $response = HttpService::apiHttp()
                ->get("bills/{$bill->number}/pdf-url")
                ->json();

            return $response['data']['pdf_url'] ?? null;
        } catch (\Exception $e) {
            Log::error('❌ ElectronicBillService::getPdfUrl - Error', [
                'bill_id' => $bill->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Obtiene la URL del XML oficial de la factura electrónica
     */
    public static function getXmlUrl(Bill $bill): ?string
    {
        if (!$bill->electronicBill || !$bill->electronicBill->is_validated) {
            return null;
        }

        try {
            $response = HttpService::apiHttp()
                ->get("bills/{$bill->number}/xml-url")
                ->json();

            return $response['data']['xml_url'] ?? null;
        } catch (\Exception $e) {
            Log::error('❌ ElectronicBillService::getXmlUrl - Error', [
                'bill_id' => $bill->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
