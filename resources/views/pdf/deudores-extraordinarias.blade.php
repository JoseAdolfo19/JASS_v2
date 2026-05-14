<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #111; background: #fff; padding: 24px; }

        /* Encabezado */
        .header { text-align: center; border-bottom: 3px solid #5b21b6; padding-bottom: 12px; margin-bottom: 16px; }
        .header h1 { font-size: 18px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .header h2 { font-size: 13px; color: #5b21b6; font-weight: bold; text-transform: uppercase; margin-top: 4px; }
        .header p  { font-size: 10px; color: #555; margin-top: 4px; }

        /* Resumen */
        .summary { display: flex; gap: 12px; margin-bottom: 16px; }
        .summary-box {
            flex: 1; border: 1px solid #ddd; border-radius: 8px;
            padding: 10px 14px; text-align: center; background: #f9f7ff;
        }
        .summary-box .val { font-size: 22px; font-weight: bold; color: #5b21b6; }
        .summary-box .lbl { font-size: 9px; text-transform: uppercase; color: #666; margin-top: 2px; }

        /* Filtro badge */
        .filter-badge {
            display: inline-block; background: #5b21b6; color: #fff;
            font-size: 9px; font-weight: bold; text-transform: uppercase;
            padding: 3px 10px; border-radius: 20px; margin-bottom: 14px;
        }

        /* Tabla */
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #5b21b6; color: #fff; }
        thead th { padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        tbody tr:nth-child(even) { background: #f5f3ff; }
        tbody tr { border-bottom: 1px solid #e5e7eb; }
        tbody td { padding: 7px 10px; font-size: 10px; vertical-align: top; }
        .name-cell { font-weight: bold; font-size: 11px; }
        .sector-cell { color: #666; font-size: 9px; }
        .cuotas-cell { color: #5b21b6; font-size: 9px; }
        .amount-cell { font-weight: bold; color: #5b21b6; text-align: right; white-space: nowrap; }
        .num-cell { color: #888; text-align: center; font-size: 10px; }

        /* Total final */
        .total-row { background: #5b21b6 !important; color: #fff; }
        .total-row td { font-weight: bold; font-size: 11px; color: #fff; padding: 9px 10px; }

        /* Pie */
        .footer { margin-top: 20px; border-top: 1px solid #ddd; padding-top: 10px; text-align: center; font-size: 9px; color: #888; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $jass['nombre'] }}</h1>
        <h2>Reporte: Deudores de Cuotas Extraordinarias</h2>
        <p>
            Cuota: <strong>{{ $filtro }}</strong> &nbsp;|&nbsp;
            Generado: {{ $fecha }}
            @if($jass['direccion']) &nbsp;|&nbsp; {{ $jass['direccion'] }} @endif
        </p>
    </div>

    {{-- Resumen --}}
    <div class="summary">
        <div class="summary-box">
            <div class="val">{{ $deudores->count() }}</div>
            <div class="lbl">Socios Deudores</div>
        </div>
        <div class="summary-box">
            <div class="val">S/ {{ number_format($total, 2) }}</div>
            <div class="lbl">Total por Cobrar</div>
        </div>
        <div class="summary-box">
            <div class="val">{{ $tipos->count() }}</div>
            <div class="lbl">Cuotas Activas</div>
        </div>
    </div>

    <div class="filter-badge">Filtro: {{ $filtro }}</div>

    @if($deudores->isEmpty())
        <p style="text-align:center; color:#555; padding: 30px 0;">
            ✓ Todos los socios han pagado las cuotas extraordinarias activas.
        </p>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width:28px;">#</th>
                    <th>Socio</th>
                    <th>Sector</th>
                    <th>Cuotas Pendientes</th>
                    <th style="text-align:center;">N°</th>
                    <th style="text-align:right;">Deuda</th>
                </tr>
            </thead>
            <tbody>
                @foreach($deudores as $i => $d)
                <tr>
                    <td class="num-cell">{{ $i + 1 }}</td>
                    <td>
                        <div class="name-cell">{{ strtoupper($d['last_name']) }}, {{ strtoupper($d['name']) }}</div>
                    </td>
                    <td class="sector-cell">{{ $d['sector'] ?? '—' }}</td>
                    <td class="cuotas-cell">{{ implode(', ', $d['cuotas_pendientes']) }}</td>
                    <td class="num-cell">{{ count($d['cuotas_pendientes']) }}</td>
                    <td class="amount-cell">S/ {{ number_format($d['total_deuda'], 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5" style="text-align:right;">TOTAL GENERAL</td>
                    <td style="text-align:right;">S/ {{ number_format($total, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="footer">
        {{ $jass['nombre'] }} &mdash; Reporte generado el {{ $fecha }} &mdash; Documento interno
    </div>

</body>
</html>