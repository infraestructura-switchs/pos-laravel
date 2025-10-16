<?php

namespace App\Services\Factro;

use App\Enums\LegalOrganization;
use App\Exceptions\CustomException;
use App\Models\Bill;
use App\Models\Company;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\FactroConfigurationService;

class FactroElectronicBillService
{

    private static function prepareData(Bill $bill): array
    {
        $details = $bill->details;
        $items = [];
        $totalBruto = 0;
        $totalImpuestos = 0;
        $tributosAggregate = [];

        foreach ($details as $detail) {
            $tribute1 = $detail->documentTaxes->first();
            $hasTax = false;
            $taxRate = 0;
            $tribute2 = null;
            $apiTributeId = null;
            $tributeName = null;

            if ($tribute1) {
                $tributeName = $tribute1->tribute_name;
                if ($tributeName && !empty(trim($tributeName))) {
                    $tribute2 = $tribute1->taxRates->first();
                    if ($tribute2 && $tribute2->rate > 0) {
                        $hasTax = true;
                        $taxRate = $tribute2->rate / 100;
                    }
                }
            }


            $valorUnitarioBruto = $detail->price;
            if ($hasTax) {
                $valorUnitarioBruto = $detail->price / (1 + $taxRate);
            }

            $valorBruto = $valorUnitarioBruto * $detail->amount;
            $totalBruto += $valorBruto;

            Log::info('Detalle de factura', [
                'precio_original' => $detail->price,
                'precio_bruto_ajustado' => $valorUnitarioBruto,
                'cantidad' => $detail->amount,
                'valor_bruto' => $valorBruto,
                'tiene_impuesto' => $hasTax,
                'tasa_impuesto' => $taxRate,
            ]);

            $valorImporte = 0;
            if ($hasTax) {
                $valorImporte = $valorBruto * $taxRate;
                $totalImpuestos += $valorImporte;

                $apiTributeId = self::$tributeMap[$tributeName] ?? null;
                if (!$apiTributeId) {
                    Log::error('apiTributeId no mapeado', ['tribute_name' => $tributeName, 'detail_id' => $detail->id]);
                } else {
                    $tributeKey = $apiTributeId;
                    if (!isset($tributosAggregate[$tributeKey])) {
                        $tributosAggregate[$tributeKey] = [
                            'id' => $apiTributeId,
                            'nombre' => $tributeName,
                            'esImpuesto' => true,
                            'valorImporteTotal' => 0,
                            'detalles' => []
                        ];
                    }
                    $tributosAggregate[$tributeKey]['valorImporteTotal'] += round($valorImporte, 2);
                    $tributosAggregate[$tributeKey]['detalles'][] = [
                        'valorImporte' => round($valorImporte, 2),
                        'valorBase' => round($valorBruto, 2),
                        'porcentaje' => (float) $tribute2->rate,
                    ];
                }
            }

            $items[] = [
                'tipoDetalle' => 1,
                'valorCodigoInterno' => $detail->product->reference ?? '',
                'codigoEstandar' => 'EA',
                'valorCodigoEstandar' => $detail->product->reference ?? '',
                'descripcion' => $detail->name,
                'unidades' => (float) $detail->amount,
                'unidadMedida' => '94',
                'valorUnitarioBruto' => round($valorUnitarioBruto, 2),
                'valorBruto' => round($valorBruto, 2),
                'cargosDescuentos' => [],
                'tributos' => $hasTax && $apiTributeId ? [
                    [
                        'id' => $apiTributeId,
                        'nombre' => $tributeName,
                        'esImpuesto' => true,
                        'valorImporte' => round($valorImporte, 2),
                        'valorBase' => round($valorBruto, 2),
                        'porcentaje' => (float) $tribute2->rate,
                    ]
                ] : []
            ];
        }

        if (empty($items)) {
            Log::error('No items válidos para Factro', ['bill_id' => $bill->id]);
            throw new CustomException('No se pudieron preparar items para Factro');
        }


        $calculatedTotal = $totalBruto - $bill->discount + $totalImpuestos;
        if (abs($calculatedTotal - $bill->total) > 0.01) {
            Log::error('Inconsistencia en total de factura', [
                'bill_id' => $bill->id,
                'calculated_total' => $calculatedTotal,
                'stored_total' => $bill->total,
                'subtotal' => $bill->subtotal,
                'discount' => $bill->discount,
                'total_impuestos' => $totalImpuestos,
            ]);
            throw new CustomException("El total almacenado ({$bill->total}) no coincide con el cálculo esperado ({$calculatedTotal}).");
        }

        $customer = $bill->customer;
        $tipoIdentificacion = $customer->identificationDocument->code ?? 31;
        Log::info('Tipo Identificación mapeado', ['customer_id' => $customer->id, 'code' => $tipoIdentificacion]);
        $tipoPersona = ($customer->legal_organization == LegalOrganization::LEGAL_PERSON->value) ? '1' : '2';

        $company = Company::first();
        $moneda = $company->currency->acronym ?? 'COP';
        Log::info('Moneda de company', ['moneda' => $moneda]);
        $dv = $customer->dv ?? '';
        Log::info('DV', ['dv' => $dv]);

        $departamento = $company->department;
        $ciudad = $company->city;
        $ubicacion = [
            'cityName' => $ciudad->city_name ?? 'tulua',
            'countrySubentity' => $departamento->department_name ?? 'CAUCA',
            'address' => [$company->direction ?? 'CL 46 NORTE AV 5 A N 36'],
        ];
        Log::info('Ubicación de company expandida', ['ubicacion' => $ubicacion]);

        $numberingRange = FactroApiService::getNumberingRangeForBill($bill->terminal_id ?? null);
        Log::info('Datos de Factro expandidos bill->number numberingRange->current', [
            'bill->number' => $bill->number,
            'numberingRange->current' => $numberingRange->current,
        ]);

        $bill->number = $bill->number ?? ($numberingRange->current + 1);
        Log::info('Datos de Factro expandidos bill->number numberingRange->current completo', [
            'bill->number' => $bill->number,
            'numberingRange->current' => $numberingRange->current,
            'numero_completo' => $numberingRange->prefix . $bill->number,
        ]);

        $data = [
            'documento' => [
                'prefijo' => $numberingRange->prefix,
                'numeroDocumento' => $bill->number,
                'numeroDocumentoCompleto' => $numberingRange->prefix . $bill->number,
                'numeroAutorizacion' => $numberingRange->resolution_number,
                'fechaInicioNumeracion' => $numberingRange->date_authorization,
                'fechaFinNumeracion' => $numberingRange->expire,
                'numeracionDesde' => $numberingRange->from,
                'numeracionHasta' => $numberingRange->to,
                'tipoDocumento' => '1',
                'tipoOperacion' => '10',
                'subTipoDocumento' => '01',
                'fechaEmision' => $bill->created_at->format('Y-m-d'),
                'horaEmision' => $bill->created_at->format('H:i:s') . '-05:00',
                'moneda' => $moneda,
                'notificaciones' => [
                    [
                        'tipo' => 1,
                        'valor' => [$customer->email]
                    ]
                ],
                'formaPago' => [
                    'tipoPago' => 1,
                    'codigoMedio' => $bill->paymentMethod->code,
                ]
            ],
            'adquiriente' => [
                'identificacion' => $customer->no_identification,
                'tipoIdentificacion' => $tipoIdentificacion,
                'razonSocial' => $customer->names ?? '',
                'correo' => $customer->email ?? '',
                'digitoVerificacion' => $dv,
                'telefono' => $customer->phone,
                'tipoPersona' => $tipoPersona,
                'ubicacion' => $ubicacion
            ],
            'totales' => [
                'valorBruto' => round($totalBruto, 2),
                'valorCargos' => 0.00,
                'valorDescuentos' => round($bill->discount, 2),
                'valorTotalSinImpuestos' => $hasTax ? round($totalBruto - $bill->discount, 2) : 0,
                'valorTotalConImpuestos' => round($totalBruto - $bill->discount + $totalImpuestos, 2),
                'valorNeto' => round($totalBruto - $bill->discount + $totalImpuestos, 2),
            ],
            'detalles' => $items,
            'tributos' => array_values($tributosAggregate),
            'versionIntegrador' => '1.0.1.2'
        ];

        Log::info('Datos preparados para FACTRO', [
            'bill_id' => $bill->id,
            'items_count' => count($items),
            'total_bruto' => $totalBruto,
            'total_impuestos' => $totalImpuestos,
            'total_neto' => $data['totales']['valorNeto']
        ]);
        Log::debug('Detalle datos para FACTRO', ['data' => $data]);

        return $data;
    }
        private static $tributeMap = [
        'IBUA' => '5',
        'IVA'  => '1',
        'INC'  => '4',
        'ICUI' => '6',
    ];


    public static function validate(Bill $bill): Response
    {
        Log::info('Iniciando validación FACTRO para bill', ['bill' => $bill]);
        $data = self::prepareData($bill);
        Log::info('Datos preparados para validación FACTRO data', ['data_keys' => array_keys($data)]);
        $httpService = FactroHttpService::apiHttp();
        $response = $httpService->post('send-invoice-public-key', $data);
        Log::info('Respuesta de FACTRO recibida', ['status' => $response->status(), 'bill_id' => $bill->id]);
        return $response;
    }

    public static function saveElectronicBill(Response $response, array $data, Bill $bill): void
    {
        $electronicBill = $data;
        Log::info('Respuesta electrónica FACTRO expandida', ['electronicBill' => $electronicBill]);
        $dianSuccess = $electronicBill['dianStatus'] === 'true';
        $httpStatus = $response->status();

        if (!$dianSuccess || $httpStatus !== 200) {
            Log::error('Error en DIAN o HTTP desde FACTRO', [
                'http_status' => $httpStatus,
                'dian_status' => $electronicBill['dianStatus'] ?? 'N/A',
                'errors' => $electronicBill['errorDian'] ?? [],
                'response_keys' => array_keys($electronicBill),
                'bill_id' => $bill->id
            ]);
            throw new CustomException($electronicBill['descripcion'] ?? 'Error en validación DIAN: ' . json_encode($electronicBill['errorDian'] ?? []));
        }

        $terminalId = $bill->terminal_id;
        $numberingRange = FactroApiService::getNumberingRangeForBill($terminalId);
        $billNumberFull = $electronicBill['prefijo'] . $electronicBill['numeroFactura'];
        $billNumber = $electronicBill['numeroFactura'];
        $bill->number = $billNumberFull;
        $bill->save();

        $qr = $electronicBill['qr'] ?? '';
        $cufe = $electronicBill['cufeOrCude'] ?? '';

        $billData = [
            'number' => $billNumberFull,
            'qr_image' => $qr,
            'cufe' => $cufe,
            'numbering_range' => $numberingRange ? json_encode($numberingRange) : null,
            'is_validated' => true,
            'id_factura' => $electronicBill['idFactura'] ?? null,
            'descripcion' => $electronicBill['descripcion'] ?? null,
            'ruta_factura' => $electronicBill['rutaFacturaContenedor'] ?? null,
            'valor_firma' => $electronicBill['valorFirma'] ?? null,
        ];

        Log::info('Guardando datos electrónicos FACTRO', [
            'qr_image' => $qr,
            'cufe' => $cufe,
            'response_keys' => array_keys($electronicBill),
            'billNumber' => $billNumberFull,
            'billData' => $billData,
        ]);

        if (!$bill->electronicBill) {
            $bill->electronicBill()->create($billData);
        } else {
            $bill->electronicBill()->update($billData);
        }

        $numberingRange->current = (int) $billNumber;
        $numberingRange->save();
        Cache::forget('factro_numbering_ranges');
        Log::info('Factura electrónica FACTRO guardada', ['bill_id' => $bill->id]);
    }

    public static function storeCreditNote(Bill $bill): void
    {
        throw new \Exception('No implementado para FACTRO aún');
    }

    public static function validateCreditNote(Bill $bill): Response
    {
        throw new \Exception('No implementado para FACTRO aún');
    }

    public static function saveCreditNote(array $data, Bill $bill): void
    {
        throw new \Exception('No implementado para FACTRO aún');
    }
}