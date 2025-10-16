<?php
namespace App\Services\Factro;

use App\Enums\LegalOrganization;
use App\Exceptions\CustomException;
use App\Models\Bill;
use App\Models\Tribute;
use App\Models\Company;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
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
            $valorUnitarioBruto = $detail->price;
            $valorBruto = $valorUnitarioBruto * $detail->amount;
            $totalBruto += $valorBruto;

            $tribute1 = $detail->documentTaxes->first();
            $hasTax = false;
            $tributeName = null;
            $apiTributeId = null;
            $tribute2 = null;
            $valorImporte = 0;

            Log::info('documentTaxes 1', ['documentTaxes' => $detail->documentTaxes]);
            if ($tribute1 ) {
                 Log::info('tribute1 1', ['tribute1' => $tribute1]);
                $tributeName = $tribute1->tribute_name; 
                if ($tributeName && !empty(trim($tributeName)) ) {
                    $tribute2 = $tribute1->taxRates->first();
                    Log::info('tribute2 2', ['tribute2' => $tribute2]);
                    if ($tribute2 && $tribute2->rate > 0) {
                        $apiTributeId = self::$tributeMap[$tributeName] ;
                        if ($apiTributeId) {
                            $hasTax = true;
                            $taxRate = $tribute2->rate / 100;
                            $valorImporte = $valorBruto * $taxRate;
                            $totalImpuestos += $valorImporte;


                            $tributeKey = $apiTributeId;
                            if (!isset($tributosAggregate[$tributeKey])) {
                                $tributosAggregate[$tributeKey] = [
                                    'id' => $apiTributeId,
                                    'nombre' => $tributeName,
                                    'esImpuesto' => true,
                                    'valorImporteTotal' => $totalImpuestos,
                                    'detalles' => []
                                ];
                            }
                            $tributosAggregate[$tributeKey]['valorImporteTotal'] += $valorImporte;
                            $tributosAggregate[$tributeKey]['detalles'][] = [
                                'valorImporte' => round($valorImporte, 2),
                                'valorBase' => round($valorBruto, 2),
                                'porcentaje' => (float) $tribute2->rate,
                            ];

                            Log::info('Tribute mapeado', ['tribute_name' => $tributeName, 'api_id' => $apiTributeId]);
                        } else {
                            Log::error('apiTributeId no mapeado', ['tribute_name' => $tributeName, 'detail_id' => $detail->id]);
                        }
                    } else {
                        Log::warning('Sin taxRate para tribute', ['tribute_id' => $tribute1->id]);
                    }
                } else {
                    Log::warning('tribute_name null o vacío para detail, skipping tax', ['detail_id' => $detail->id]);
                }
            } else {
                 Log::info('Sin tributo para detail', ['detail_id' => $detail->id]);
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
                'tributos' => $hasTax ? [
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
        $config = FactroConfigurationService::apiConfiguration();

        Log::info('Datos de Factro expandidos bill->number numberingRange->current', ['bill->number' => $bill->number,$numberingRange->current]);
        $bill->number = $bill->number ?? ($numberingRange->current + 1);
        
        Log::info('Datos de Factro expandidos bill->number numberingRange->current completo', 
        ['bill->number' => $bill->number,$numberingRange->current,
       $numberingRange->prefix.$bill->number]);


        $data = [
            'documento' => [
                'prefijo' => $numberingRange->prefix,
                'numeroDocumento' => $bill->number,
                'numeroDocumentoCompleto' => $numberingRange->prefix.$bill->number,
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
                'valorNeto' => round($bill->total, 2)
            ],
            'detalles' => $items,
            'tributos' => array_values($tributosAggregate), 
            'versionIntegrador' => '1.0.1.2'
        ];

        Log::info('Datos preparados para FACTRO', ['bill_id' => $bill->id, 'items_count' => count($items), 'total_bruto' => $totalBruto]);
        Log::debug('Detalle datos para FACTRO', ['data' => $data]);
        return $data;
    }
        private static $tributeMap = [
        'IBUA' => '05', 
        'IVA' => '01',  
        'INC' => '04',  
        'ICUI' => '06',
    ];


    public static function validate(Bill $bill): Response
    {
        Log::info('Iniciando validación FACTRO para bill', ['bill' => $bill]);
        $data = self::prepareData($bill);
        Log::info('Datos preparados para validación FACTRO data  ', ['$data ' => $data ]);
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
        $billNumberFull = $electronicBill['prefijo'].$electronicBill['numeroFactura'];
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