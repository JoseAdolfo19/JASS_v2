<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Reportes Completos - {{ date('d/m/Y') }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #1f2937; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #111827; }
        .header p { margin: 5px 0; color: #6b7280; }
        .section { margin-bottom: 30px; }
        .section h2 { font-size: 18px; margin-bottom: 10px; color: #111827; }
        .cards { display: flex; flex-wrap: wrap; gap: 10px; }
        .card { border: 1px solid #d1d5db; padding: 16px; border-radius: 12px; min-width: 180px; background: #f9fafb; }
        .card strong { display: block; margin-bottom: 6px; color: #111827; }
        .table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .table th, .table td { padding: 10px; border: 1px solid #d1d5db; text-align: left; }
        .table th { background: #f3f4f6; }
        .small { font-size: 12px; color: #6b7280; }
        .footer { margin-top: 40px; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reportes Completos</h1>
        <p>Resumen general: morosos, caja, altas/bajas, multas y aptos para corte</p>
        <p>Fecha de generación: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <h2>Balance de Caja</h2>
        <div class="cards">
            <div class="card">
                <strong>Ingresos</strong>
                S/ {{ number_format($data['balance']['ingresos'], 2) }}
            </div>
            <div class="card">
                <strong>Egresos</strong>
                S/ {{ number_format($data['balance']['egresos'], 2) }}
            </div>
            <div class="card">
                <strong>Saldo</strong>
                S/ {{ number_format($data['balance']['saldo'], 2) }}
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Altas y Bajas</h2>
        <div class="cards">
            <div class="card">
                <strong>Altas</strong>
                {{ $data['altasBajas']['altas'] }}
            </div>
            <div class="card">
                <strong>Bajas</strong>
                {{ $data['altasBajas']['bajas'] }}
            </div>
            <div class="card">
                <strong>Suspendidos</strong>
                {{ $data['altasBajas']['suspendidos'] }}
            </div>
            <div class="card">
                <strong>Crecimiento Neto</strong>
                {{ $data['altasBajas']['crecimiento_neto'] }}
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Morosos</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Socio</th>
                    <th>Sector</th>
                    <th>Meses deuda</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $morososGrouped = collect($data['morosos'])
                        ->groupBy(fn($item) => $item['associate']['sector'] ?? 'Sin Sector')
                        ->sortKeys()
                        ->map(fn($items) => $items->sortBy(fn($item) => $item['associate']['last_name'] . ', ' . $item['associate']['name']));
                @endphp
                
                @foreach($morososGrouped as $sector => $items)
                    <tr style="background-color: #e5e7eb; font-weight: bold;">
                        <td colspan="4">SECTOR: {{ $sector }}</td>
                    </tr>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item['associate']['last_name'] }}, {{ $item['associate']['name'] }}</td>
                        <td>{{ $item['associate']['sector'] }}</td>
                        <td>{{ $item['meses_deuda'] }}</td>
                        <td>S/ {{ number_format($item['total'], 2) }}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
        <p class="small">Listado generado automáticamente de socios sin pagos en los últimos 2 meses.</p>
    </div>

    <div class="section">
        <h2>Multas</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Socio</th>
                    <th>Sector</th>
                    <th>Cantidad de multas</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $multasGrouped = collect($data['multas'])
                        ->groupBy(fn($item) => $item['associate']['sector'] ?? 'Sin Sector')
                        ->sortKeys()
                        ->map(fn($items) => $items->sortBy(fn($item) => $item['associate']['last_name'] . ', ' . $item['associate']['name']));
                @endphp
                
                @foreach($multasGrouped as $sector => $items)
                    <tr style="background-color: #e5e7eb; font-weight: bold;">
                        <td colspan="4">SECTOR: {{ $sector }}</td>
                    </tr>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item['associate']['last_name'] }}, {{ $item['associate']['name'] }}</td>
                        <td>{{ $item['associate']['sector'] }}</td>
                        <td>{{ $item['cantidad_multas'] }}</td>
                        <td>S/ {{ number_format($item['total_multas'], 2) }}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Aptos para Corte</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Socio</th>
                    <th>Sector</th>
                    <th>Meses deuda</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $aptosGrouped = collect($data['aptosCorte'])
                        ->groupBy(fn($item) => $item['associate']['sector'] ?? 'Sin Sector')
                        ->sortKeys()
                        ->map(fn($items) => $items->sortBy(fn($item) => $item['associate']['last_name'] . ', ' . $item['associate']['name']));
                @endphp
                
                @foreach($aptosGrouped as $sector => $items)
                    <tr style="background-color: #e5e7eb; font-weight: bold;">
                        <td colspan="3">SECTOR: {{ $sector }}</td>
                    </tr>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item['associate']['last_name'] }}, {{ $item['associate']['name'] }}</td>
                        <td>{{ $item['associate']['sector'] }}</td>
                        <td>{{ $item['meses_deuda'] }}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Reporte completado por JASS - {{ date('Y') }}</p>
    </div>
</body>
</html>
