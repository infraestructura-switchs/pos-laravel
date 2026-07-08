<footer>
    @php
        $providerName = optional($company->invoiceProvider)->name ?? ($company->name ?? 'Sistema POS');
        $providerUrl = optional($company->invoiceProvider)->url ?? (config('app.url') ?: url('/'));
        $providerNit = optional($company->invoiceProvider)->nit ?? ($company->nit ?? 'N/D');
    @endphp
    <table  class="w-full">
        <tr>
            <td class="text-center text-sm">
                Elaborado por: {{ $providerName }} | {{ $providerUrl }} | NIT: {{ $providerNit }}
            </td>
        </tr>
        <tr>
            <td class="text-right text-xs">
                página {PAGENO} de {nbpg}
            </td>
        </tr>
    </table>
</footer>
