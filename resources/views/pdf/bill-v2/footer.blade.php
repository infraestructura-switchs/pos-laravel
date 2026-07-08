<footer>
    @php
        $invoiceProvider = data_get($company, 'invoiceProvider');
        $providerName = optional($invoiceProvider)->name ?? ($company->name ?? 'Sistema POS');
        $providerUrl = optional($invoiceProvider)->url ?? (config('app.url') ?: url('/'));
        $providerNit = optional($invoiceProvider)->nit ?? ($company->nit ?? 'N/D');
    @endphp
    <table style="width: 100%; font-size: 9px; color: #1f2937; border-top: 1px solid #9ca3af; margin-top: 4px;">
        <tr>
            <td style="text-align: left;">Factura elaborada y enviada por el proveedor tecnológico a través del software de facturación electrónica de {{ $providerName }} {{ $providerNit }}</td>
            <td style="text-align: right; white-space: nowrap;">{PAGENO}/{nbpg}</td>
        </tr>
    </table>
</footer>
