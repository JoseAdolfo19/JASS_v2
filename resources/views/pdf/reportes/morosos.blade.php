<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Padrón General de Morosos - {{ date('d/m/Y') }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #1a365d; margin: 0; }
        .header p { color: #666; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .total-row { background-color: #e8f4f8; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #1a365d; margin: 0; }
        .header p { color: #666; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .total-row { background-color: #e8f4f8; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PADRÓN GENERAL DE MOROSOS</h1>
        <p>Lista automatizada de socios con pagos pendientes</p>
        <p>Fecha de generación: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Socio</th>
                <th>Sector</th>
                <th>Meses Deuda</th>
                <th>Subtotal</th>
                <th>Mora</th>
                <th>Total Deuda</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item['associate']['last_name'] }}, {{ $item['associate']['name'] }}</td>
                <td>{{ $item['associate']['sector'] }}</td>
                <td>{{ $item['meses_deuda'] }}</td>
                <td>S/ {{ number_format($item['subtotal'], 2) }}</td>
                <td>S/ {{ number_format($item['mora'], 2) }}</td>
                <td><strong>S/ {{ number_format($item['total'], 2) }}</strong></td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3"><strong>TOTAL GENERAL</strong></td>
                <td><strong>S/ {{ number_format($data->sum('subtotal'), 2) }}</strong></td>
                <td><strong>S/ {{ number_format($data->sum('mora'), 2) }}</strong></td>
                <td><strong>S/ {{ number_format($data->sum('total'), 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte generado por el Sistema de Gestión JASS - {{ date('Y') }}</p>
        <p>Identificación rápida para cobranza en campo</p>
    </div>
</body>
</html>