<div class="header">
    <img src="{{ public_path('images/logo.png') }}" alt="Logo Empresa" style="width: 50px; height: auto; float: left;">
    <h2 style="text-align: center; margin-bottom: 10px;">Remisión de Inventario</h2>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <tr>
            <td style="width: 50%; padding: 5px;"><strong>Folio:</strong> {{ $remission->folio }}</td>
            <td style="width: 50%; padding: 5px;"><strong>Fecha:</strong> {{ $remission->remission_date->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td style="padding: 5px;"><strong>Concepto:</strong> {{ $remission->concept }}</td>
            <td style="padding: 5px;"><strong>Nota:</strong> {{ $remission->note ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 5px;"><strong>Bodega:</strong> {{ $remission->warehouse->name ?? 'N/A' }}</td>
            <td style="padding: 5px;"><strong>Usuario:</strong> {{ $remission->user->name ?? 'N/A' }}</td>
        </tr>
    </table>
</div>