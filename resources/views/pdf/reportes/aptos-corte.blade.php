<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Aptos para Corte - {{ date('d/m/Y') }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #dc2626; margin: 0; }
        .header p { color: #666; margin: 5px 0; }
        .alert { background-color: #fef2f2; border: 2px solid #dc2626; padding: 15px; border-radius: 10px; margin: 20px 0; text-align: center; }
        .alert strong { color: #dc2626; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .urgent { background-color: #fef2f2; }
        .urgent td { color: #000000; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LISTA DE "APTOS PARA CORTE"</h1>
        <p>Fecha de generación: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="alert">
        <strong>¡ATENCIÓN!</strong> Esta lista contiene socios que superan los 3 meses de deuda.<br>
        Se recomienda ejecutar sanciones y cortes de servicio según estatuto.
    </div>

    <table>
        <thead>
            <tr>
                <th>Socio</th>
                <th>Sector</th>
                <th>Meses de Deuda</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr class="urgent">
                <td>{{ $item['associate']['last_name'] }}, {{ $item['associate']['name'] }}</td>
                <td>{{ $item['associate']['sector'] }}</td>
                <td>{{ $item['meses_deuda'] }}</td>
                <td><strong>APTO PARA CORTE</strong></td>
            </tr>
            @endforeach

            @if($data->count() == 0)
            <tr>
                <td colspan="4" style="text-align: center; color: #10b981; background-color: #f0fdf4;">
                    <strong>¡Excelente!</strong> No hay socios aptos para corte en este momento.
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte generado por el Sistema de Gestión JASS - {{ date('Y') }}</p>
        <p>Ejecución de sanciones según estatuto</p>
    </div>
</body>
</html>