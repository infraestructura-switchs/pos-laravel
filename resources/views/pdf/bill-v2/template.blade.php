<!DOCTYPE html>
<html lang="es">
<body>
@php
    $electronicBill = $bill->electronicBill;
    $electronicRange = $electronicBill?->numbering_range;
    if (is_array($electronicRange)) {
        $electronicRange = (object) $electronicRange;
    }

    $activeRange = $electronicRange ?: $range;

    $createdDate = $bill->created_at?->format('Y-m-d');
    $createdTime = $bill->created_at?->format('H:i:sP');
    $dueDate = $bill->finance?->due_date?->format('Y-m-d');

    $issuerName = $company->name ?? '';
    $issuerNit = $company->nit ?? '';
    $issuerPhone = $company->phone ?? '';
    $issuerEmail = $company->email ?? '';
    $issuerAddress = $company->direction ?? '';

    $customer = data_get($bill, 'customer');
    $buyerName = data_get($bill, 'customer.names', 'Consumidor Final');
    $buyerNit = data_get($bill, 'customer.format_no_identification', data_get($bill, 'customer.no_identification', ''));
    $buyerPhone = data_get($bill, 'customer.phone', '');
    $buyerEmail = data_get($bill, 'customer.email', '');
    $buyerAddress = data_get($bill, 'customer.direction', '');

    $subTotal = (float) ($bill->subtotal ?? 0);
    $totalTax = (float) ($bill->documentTaxes?->sum('tax_amount') ?? 0);
    $totalDiscount = (float) ($bill->discount ?? 0);
    $grandTotal = (float) ($bill->final_total ?? $bill->total ?? 0);

    $paymentMethod = data_get($bill, 'paymentMethod.name', 'No definido');
    $paymentType = optional($bill->finance)->id ? 'Credito' : 'Contado';
    $itemCount = $bill->details->count();
    $orderReference = $electronicBill->order_reference ?? '';
    $currencyCode = 'COP';
@endphp

<div style="font-family: DejaVu Sans Condensed, DejaVu Sans, sans-serif; color: #111827; font-size: 8.45px; line-height: 1.14;">
    <div style="text-align: center; margin-bottom: 7px; border-bottom: 1px solid #9ca3af; padding-bottom: 4px;">
        <div style="font-weight: 700; font-size: 12.4px; letter-spacing: 0.2px;">FACTURA ELECTRÓNICA DE VENTA {{ $bill->number }}</div>
    </div>

    <table style="width: 72%; margin-bottom: 6px; margin-left: auto;">
        <tr>
            <td style="width: 32%; font-weight: 700;">Fecha de Exp.:</td>
            <td style="width: 68%;">{{ $createdDate }}</td>
        </tr>
        <tr>
            <td style="font-weight: 700;">Hora de Exp.:</td>
            <td>{{ $createdTime }}</td>
        </tr>
        @if ($dueDate)
            <tr>
                <td style="font-weight: 700;">Fecha de Ven.:</td>
                <td>{{ $dueDate }}</td>
            </tr>
        @endif
        @if ($electronicBill)
            <tr>
                <td style="font-weight: 700;">Fecha y hora de validación:</td>
                <td>{{ optional($electronicBill->created_at)->format('Y-m-d H:i:s') }}</td>
            </tr>
        @endif
    </table>

    @if ($activeRange && !empty($activeRange->resolution_number))
        <div style="margin: 3px 0 7px 0; border: 1px solid #374151; padding: 3px 4px; font-size: 8px; background: #f9fafb; line-height: 1.16;">
            N°. Resolución: {{ $activeRange->resolution_number }}
            @if (!empty($activeRange->start_date))
                válida desde {{ $activeRange->start_date }}
            @elseif (!empty($activeRange->date_authorization))
                válida desde {{ $activeRange->date_authorization }}
            @endif
            @if (!empty($activeRange->end_date))
                hasta {{ $activeRange->end_date }}
            @endif
            @if (!empty($activeRange->prefix))
                rango desde {{ $activeRange->prefix }}{{ $activeRange->from ?? '' }} hasta {{ $activeRange->prefix }}{{ $activeRange->to ?? '' }}
            @endif
        </div>
    @endif

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 7px; table-layout: fixed;">
        <tr>
            <td style="width: 50%; border: 1px solid #374151; vertical-align: top; padding: 0; line-height: 1.1;">
                <div style="font-weight: 700; margin-bottom: 0; font-size: 8.95px; background: #f3f4f6; border-bottom: 1px solid #374151; padding: 3px 4px;">DATOS EMISOR</div>
                <div style="padding: 3px 4px;">
                <div><b>Razón Social:</b> {{ $issuerName }}</div>
                <div><b>NIT:</b> {{ $issuerNit }}</div>
                <div><b>Actividad:</b> </div>
                <div><b>Dirección:</b> {{ $issuerAddress }}</div>
                <div><b>Teléfono:</b> {{ $issuerPhone }}</div>
                <div><b>E-mail:</b> {{ $issuerEmail }}</div>
                </div>
            </td>
            <td style="width: 50%; border: 1px solid #374151; vertical-align: top; padding: 0; line-height: 1.1;">
                <div style="font-weight: 700; margin-bottom: 0; font-size: 8.95px; background: #f3f4f6; border-bottom: 1px solid #374151; padding: 3px 4px;">DATOS ADQUIRIENTE</div>
                <div style="padding: 3px 4px;">
                <div><b>Razón Social:</b> {{ $buyerName }}</div>
                <div><b>NIT:</b> {{ $buyerNit }}</div>
                <div><b>Contacto:</b> </div>
                <div><b>Dirección:</b> {{ $buyerAddress }}</div>
                <div><b>Teléfono:</b> {{ $buyerPhone }}</div>
                <div><b>E-mail:</b> {{ $buyerEmail }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 7px; table-layout: fixed;">
        <tr>
            <th style="width: 10%; border: 1px solid #374151; padding: 2px 3px; font-size: 7.8px; background: #f3f4f6;">MONEDA</th>
            <th style="width: 8%; border: 1px solid #374151; padding: 2px 3px; font-size: 7.8px; background: #f3f4f6;">No. LINEAS</th>
            <th style="width: 16%; border: 1px solid #374151; padding: 2px 3px; font-size: 7.8px; background: #f3f4f6;">ORDEN DE COMPRA</th>
            <th style="width: 15%; border: 1px solid #374151; padding: 2px 3px; font-size: 7.8px; background: #f3f4f6;">FECHA DE ORDEN</th>
            <th style="width: 20%; border: 1px solid #374151; padding: 2px 3px; font-size: 7.8px; background: #f3f4f6;">MEDIO DE PAGO</th>
            <th style="width: 16%; border: 1px solid #374151; padding: 2px 3px; font-size: 7.8px; background: #f3f4f6;">FORMA DE PAGO</th>
            <th style="width: 15%; border: 1px solid #374151; padding: 2px 3px; font-size: 7.8px; background: #f3f4f6;">VALOR</th>
        </tr>
        <tr>
            <td style="border: 1px solid #374151; text-align: center; padding: 2px 3px;">{{ $currencyCode }}</td>
            <td style="border: 1px solid #374151; text-align: center; padding: 2px 3px;">{{ $itemCount }}</td>
            <td style="border: 1px solid #374151; text-align: center; padding: 2px 3px;">{{ $orderReference }}</td>
            <td style="border: 1px solid #374151; text-align: center; padding: 2px 3px;">{{ $createdDate }}</td>
            <td style="border: 1px solid #374151; text-align: center; padding: 2px 3px;">{{ $paymentMethod }}</td>
            <td style="border: 1px solid #374151; text-align: center; padding: 2px 3px;">{{ $paymentType }}</td>
            <td style="border: 1px solid #374151; text-align: right; padding: 2px 3px;">@formatToCop($grandTotal)</td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 7px; table-layout: fixed;">
        <tr>
            <th style="border: 1px solid #374151; padding: 2px 3px; text-align: center; font-size: 7.9px; background: #f3f4f6;">NOTAS</th>
        </tr>
        <tr>
            <td style="border: 1px solid #374151; padding: 3px 4px; min-height: 18px;">{{ $bill->observation ?? '' }}</td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px; table-layout: fixed;">
        <tr>
            <th style="width: 4%; border: 1px solid #374151; padding: 1px 2px; font-size: 7.85px; background: #f3f4f6;">#</th>
            <th style="width: 6%; border: 1px solid #374151; padding: 1px 2px; font-size: 7.85px; background: #f3f4f6;">CANT.</th>
            <th style="width: 6%; border: 1px solid #374151; padding: 1px 2px; font-size: 7.85px; background: #f3f4f6;">COD.</th>
            <th style="width: 5%; border: 1px solid #374151; padding: 1px 2px; font-size: 7.85px; background: #f3f4f6;">U.M</th>
            <th style="width: 24%; border: 1px solid #374151; padding: 1px 2px; font-size: 7.85px; background: #f3f4f6;">DESCRIPCIÓN</th>
            <th style="width: 11%; border: 1px solid #374151; padding: 1px 2px; font-size: 7.85px; background: #f3f4f6;">VALOR UNIT.</th>
            <th style="width: 13%; border: 1px solid #374151; padding: 1px 2px; font-size: 7.85px; background: #f3f4f6;">VALOR SIN IMP.</th>
            <th style="width: 14%; border: 1px solid #374151; padding: 1px 2px; font-size: 7.85px; background: #f3f4f6;">IMPUESTOS</th>
            <th style="width: 7%; border: 1px solid #374151; padding: 1px 2px; font-size: 7.85px; background: #f3f4f6;">DCTO.</th>
            <th style="width: 10%; border: 1px solid #374151; padding: 1px 2px; font-size: 7.85px; background: #f3f4f6;">VALOR</th>
        </tr>
        @foreach ($bill->details as $index => $item)
            @php
                $itemTaxes = collect(data_get($item, 'documentTaxes', []));
                $itemTaxRates = $itemTaxes->flatMap(function ($documentTax) {
                    return collect(data_get($documentTax, 'taxRates', []))->map(function ($taxRate) use ($documentTax) {
                        $rateValue = $taxRate->has_percentage
                            ? rtrim(rtrim(number_format((float) $taxRate->rate, 2, '.', ''), '0'), '.') . '%'
                            : @formatToCop($taxRate->rate);

                        return [
                            'label' => trim(($documentTax->tribute_name ?? 'IVA') . ' ' . $rateValue),
                            'tax_amount' => (float) ($taxRate->tax_amount ?? 0),
                        ];
                    });
                });

                $taxLabel = $itemTaxRates->pluck('label')->filter()->implode(' | ');
                $taxAmount = $itemTaxRates->sum('tax_amount');
                $itemSubtotal = ((float) ($item->total ?? 0) - (float) $taxAmount) + (float) ($item->discount ?? 0);
                $itemAmount = data_get($item, 'amount', data_get($item, 'quantity', 0));
                $productReference = data_get($item, 'product.reference', '');
                $unitMeasureCode = data_get($item, 'product.unitMeasure.code', 'C62');
                $itemPrice = (float) data_get($item, 'price', 0);
                $itemDiscount = (float) data_get($item, 'discount', 0);
                $itemTotal = (float) data_get($item, 'total', 0);
            @endphp
            <tr>
                <td style="border: 1px solid #374151; text-align: center; padding: 1px 2px; font-size: 8.1px;">{{ $index + 1 }}</td>
                <td style="border: 1px solid #374151; text-align: center; padding: 1px 2px; font-size: 8.1px;">{{ $itemAmount }}</td>
                <td style="border: 1px solid #374151; text-align: center; padding: 1px 2px; font-size: 8.1px;">{{ $productReference }}</td>
                <td style="border: 1px solid #374151; text-align: center; padding: 1px 2px; font-size: 8.1px;">{{ $unitMeasureCode }}</td>
                <td style="border: 1px solid #374151; padding: 1px 2px; line-height: 1.02; font-size: 8.1px;">{{ data_get($item, 'name', '') }}</td>
                <td style="border: 1px solid #374151; text-align: right; padding: 1px 2px; font-size: 8.1px;">@formatToCop($itemPrice)</td>
                <td style="border: 1px solid #374151; text-align: right; padding: 1px 2px; font-size: 8.1px;">@formatToCop($itemSubtotal)</td>
                <td style="border: 1px solid #374151; text-align: center; padding: 1px 2px; line-height: 1.02; font-size: 8.1px;">
                    <div>{{ $taxLabel ?: 'IVA 0%' }}</div>
                    <div style="text-align: right; margin-top: 0;">@formatToCop($taxAmount)</div>
                </td>
                <td style="border: 1px solid #374151; text-align: right; padding: 1px 2px; font-size: 8.1px;">@formatToCop($itemDiscount)</td>
                <td style="border: 1px solid #374151; text-align: right; padding: 1px 2px; font-size: 8.1px;">@formatToCop($itemTotal)</td>
            </tr>
        @endforeach
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 5px; table-layout: fixed;">
        <tr>
            <td style="width: 56%; vertical-align: top;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <th style="width: 20%; border: 1px solid #374151; padding: 2px 3px; font-size: 7.65px; background: #f3f4f6;">TIPO</th>
                        <th style="width: 57%; border: 1px solid #374151; padding: 2px 3px; font-size: 7.65px; background: #f3f4f6;">DESCRIPCIÓN</th>
                        <th style="width: 23%; border: 1px solid #374151; padding: 2px 3px; font-size: 7.65px; background: #f3f4f6;">VALOR</th>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #374151; padding: 2px 3px;">Cargos</td>
                        <td style="border: 1px solid #374151; padding: 2px 3px;">-</td>
                        <td style="border: 1px solid #374151; text-align: right; padding: 2px 3px;">@formatToCop(0)</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #374151; padding: 2px 3px;">Descuentos</td>
                        <td style="border: 1px solid #374151; padding: 2px 3px;">Global</td>
                        <td style="border: 1px solid #374151; text-align: right; padding: 2px 3px;">@formatToCop($totalDiscount)</td>
                    </tr>
                </table>
            </td>
            <td style="width: 44%; vertical-align: top; padding-left: 8px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr><td style="border: 1px solid #374151; padding: 2px 3px;"><b>SubTotal</b></td><td style="border: 1px solid #374151; text-align: right; padding: 2px 3px;">@formatToCop($subTotal)</td></tr>
                    <tr><td style="border: 1px solid #374151; padding: 2px 3px;"><b>Total Imp.</b></td><td style="border: 1px solid #374151; text-align: right; padding: 2px 3px;">@formatToCop($totalTax)</td></tr>
                    <tr><td style="border: 1px solid #374151; padding: 2px 3px;"><b>Reteiva</b></td><td style="border: 1px solid #374151; text-align: right; padding: 2px 3px;">@formatToCop(0)</td></tr>
                    <tr><td style="border: 1px solid #374151; padding: 2px 3px;"><b>ReteRenta</b></td><td style="border: 1px solid #374151; text-align: right; padding: 2px 3px;">@formatToCop(0)</td></tr>
                    <tr><td style="border: 1px solid #374151; padding: 2px 3px;"><b>Reteica</b></td><td style="border: 1px solid #374151; text-align: right; padding: 2px 3px;">@formatToCop(0)</td></tr>
                    <tr><td style="border: 1px solid #374151; padding: 3px 3px; background: #eef2f7;"><b>Valor Total</b></td><td style="border: 1px solid #374151; text-align: right; padding: 3px 3px; background: #eef2f7;"><b>@formatToCop($grandTotal)</b></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table style="width: 100%; margin-top: 4px; table-layout: fixed;">
        <tr>
            <td style="width: 18%; vertical-align: top;">
                @if (!empty($electronicBill?->qr_image))
                    <img src="{{ $electronicBill->qr_image }}" style="width: 95px; height: 95px;" />
                @endif
            </td>
            <td style="width: 82%; vertical-align: top; font-size: 8px; padding-left: 6px;">
                <table style="width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 2px;">
                    <tr>
                        <th style="width: 26%; border: 1px solid #374151; padding: 2px 3px; font-size: 7.7px; background: #f3f4f6;">MONEDA</th>
                        <th style="width: 36%; border: 1px solid #374151; padding: 2px 3px; font-size: 7.7px; background: #f3f4f6;">TASA REPRESENTATIVA</th>
                        <th style="width: 38%; border: 1px solid #374151; padding: 2px 3px; font-size: 7.7px; background: #f3f4f6;">VALOR FACTURA</th>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #374151; padding: 2px 3px;">{{ $currencyCode }}</td>
                        <td style="border: 1px solid #374151; padding: 2px 3px; text-align: center;">1</td>
                        <td style="border: 1px solid #374151; padding: 2px 3px; text-align: right;">@formatToCop($grandTotal)</td>
                    </tr>
                </table>
                <div style="border: 1px solid #374151; border-top: 0; padding: 3px 4px; line-height: 1.12;">
                    @if (!empty($electronicBill?->signature))
                        <div><b>FIRMA:</b> {{ $electronicBill->signature }}</div>
                    @endif
                    @if (!empty($electronicBill?->cufe))
                        <div style="margin-top: 2px;"><b>CUFE:</b> {{ $electronicBill->cufe }}</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
