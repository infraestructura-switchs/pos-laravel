<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Remisión {{ $remission->folio }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #000; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; font-weight: bold; }
        .total-section { text-align: right; margin-top: 10px; }
        .total-section table { width: 50%; margin-left: auto; }
        .total-section td { font-weight: bold; padding: 5px; }
        .logo { width: 50px; height: auto; float: left; }
        .footer { margin-top: 40px; display: flex; justify-content: space-between; }
    </style>
</head>
<body>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Costo Unitario</th>
                <th>Costo Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($remission->remissionDetails as $detail)
                <tr>
                    <td>{{ $detail->id }}</td>
                    <td>{{ $detail->product->name ?? 'N/A' }}</td>
                    <td>{{ number_format($detail->quantity, 2) }}</td>
                    <td>${{ number_format($detail->unit_cost, 2) }}</td>
                    <td>${{ number_format($detail->total_cost, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No hay detalles.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="total-section">
        <table>
            <tr>
                <td>Subtotal</td>
                <td>${{ number_format($remission->remissionDetails->sum('total_cost'), 2) }}</td>
            </tr>
            <tr>
                <td>IVA (19%)</td>
                <td>${{ number_format($remission->remissionDetails->sum('total_cost') * 0.19, 2) }}</td>
            </tr>
            <tr style="font-size: 14px;">
                <td><strong>Total</strong></td>
                <td><strong>${{ number_format($remission->remissionDetails->sum('total_cost') * 1.19, 2) }}</strong></td>
            </tr>
        </table>
    </div>
</body>
</html>