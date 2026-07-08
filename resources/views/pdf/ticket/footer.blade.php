<footer>
    @php
        $providerName = optional($company->invoiceProvider)->name ?? ($company->name ?? 'Sistema POS');
        $providerUrl = optional($company->invoiceProvider)->url ?? (config('app.url') ?: url('/'));
        $providerNit = optional($company->invoiceProvider)->nit ?? ($company->nit ?? 'N/D');
    @endphp
    <table  class="w-full">
        <tr>
            <td class="text-center text-xs">
                Elaborado por: {{ $providerName }} <br>
                {{ $providerUrl }} NIT: {{ $providerNit }}
            </td>
        </tr>
    </table>
</footer>
