<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Resumen de Deuda por Multas - {{ date('d/m/Y') }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #1a365d; margin: 0; }
        .header p { color: #666; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .total-row { background-color: #fef3c7; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RESUMEN DE DEUDA POR MULTAS DE FAENAS Y ASAMBLEAS</h1>
        <p>Separación de ingresos por conceptos especiales</p>
        <p>Fecha de generación: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Socio</th>
                <th>Sector</th>
                <th>Cantidad de Multas</th>
                <th>Total Multas</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Agrupar por sector y ordenar alfabéticamente dentro de cada sector
                $dataGrouped = collect($data)
                    ->groupBy(fn($item) => $item['associate']['sector'] ?? 'Sin Sector')
                    ->sortKeys()
                    ->map(fn($items) => $items->sortBy(fn($item) => $item['associate']['last_name'] . ', ' . $item['associate']['name']));
            @endphp
            
            @forelse($dataGrouped as $sector => $items)
                {{-- Encabezado de sector --}}
                <tr style="background-color: #e5e7eb; font-weight: bold;">
                    <td colspan="4">SECTOR: {{ $sector }}</td>
                </tr>
                
                @foreach($items as $item)
                <tr>
                    <td>{{ $item['associate']['last_name'] }}, {{ $item['associate']['name'] }}</td>
                    <td>{{ $item['associate']['sector'] }}</td>
                    <td>{{ $item['cantidad_multas'] }}</td>
                    <td><strong>S/ {{ number_format($item['total_multas'], 2) }}</strong></td>
                </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #666;">No hay multas registradas</td>
                </tr>
            @endforelse
            
            @if(count($data) > 0)
            <tr class="total-row">
                <td colspan="3"><strong>TOTAL GENERAL</strong></td>
                <td><strong>S/ {{ number_format($data->sum('total_multas'), 2) }}</strong></td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte generado por el Sistema de Gestión JASS - {{ date('Y') }}</p>
        <p>Cobro focalizado durante reuniones comunales</p>
    </div>
</body>
</html>