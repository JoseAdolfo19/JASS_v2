<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Recibo N° {{ $payment->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            background: #fff;
            width: 72mm;
            padding: 4mm;
        }

        /* ── CABECERA ── */
        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }

        .header .jass-nombre {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header .jass-sub {
            font-size: 9px;
            color: #444;
            margin-top: 2px;
        }

        .header .recibo-nro {
            font-size: 13px;
            font-weight: bold;
            margin-top: 6px;
            letter-spacing: 2px;
        }

        .header .fecha {
            font-size: 9px;
            color: #555;
            margin-top: 2px;
        }

        /* ── DATOS DEL SOCIO ── */
        .seccion {
            margin-bottom: 8px;
        }

        .seccion-titulo {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #555;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
            margin-bottom: 4px;
        }

        .fila {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .fila .label {
            color: #555;
            font-size: 9px;
        }

        .fila .valor {
            font-weight: bold;
            font-size: 10px;
            text-align: right;
        }

        /* ── TABLA DE MESES ── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 10px;
        }

        table thead tr {
            background: #f0f0f0;
        }

        table th {
            text-align: left;
            padding: 3px 4px;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table th:last-child,
        table td:last-child {
            text-align: right;
        }

        table td {
            padding: 3px 4px;
            border-bottom: 1px dotted #ddd;
        }

        /* ── TOTALES ── */
        .totales {
            border-top: 1px dashed #000;
            padding-top: 6px;
            margin-bottom: 8px;
        }

        .fila-total {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 10px;
        }

        .fila-total.mora {
            color: #c00;
        }

        .fila-total.total-final {
            font-size: 14px;
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 4px;
            margin-top: 4px;
        }

        /* ── PIE ── */
        .footer {
            border-top: 2px dashed #000;
            padding-top: 6px;
            text-align: center;
            font-size: 8px;
            color: #555;
        }

        .footer .firma-linea {
            border-top: 1px solid #000;
            width: 60%;
            margin: 16px auto 4px;
        }

        .sello {
            font-size: 8px;
            color: #777;
            margin-top: 4px;
        }
    </style>
</head>

<body>

    {{-- ── CABECERA ── --}}
    <div class="header">
        <div class="jass-nombre">{{ $jass['nombre'] }}</div>
        @if($jass['direccion'])
        <div class="jass-sub">{{ $jass['direccion'] }}</div>
        @endif
        <div class="jass-sub">Servicio de Agua Potable</div>
        <div class="recibo-nro">RECIBO N° {{ $payment->invoice_number }}</div>
        <div class="fecha">Emitido: {{ $fecha_emision }}</div>
    </div>

    {{-- ── DATOS DEL SOCIO ── --}}
    <div class="seccion">
        <div class="seccion-titulo">Datos del Socio</div>
        <div class="fila">
            <span class="label">Socio:</span>
            <span class="valor">{{ strtoupper($asociado->last_name) }}, {{ strtoupper($asociado->name) }}</span>
        </div>
        <div class="fila">
            <span class="label">DNI:</span>
            <span class="valor">{{ $asociado->dni }}</span>
        </div>
        @if($asociado->meter_number)
        <div class="fila">
            <span class="label">N° Medidor:</span>
            <span class="valor">{{ $asociado->meter_number }}</span>
        </div>
        @endif
        <div class="fila">
            <span class="label">Sector:</span>
            <span class="valor">{{ $asociado->sector->name ?? '—' }}</span>
        </div>
        @if($asociado->address)
        <div class="fila">
            <span class="label">Dirección:</span>
            <span class="valor">{{ $asociado->address }}</span>
        </div>
        @endif
    </div>

    {{-- ── DETALLE DE MESES ── --}}
    <div class="seccion">
        <div class="seccion-titulo">Detalle de Pago</div>
        <table>
            <thead>
                <tr>
                    <th>Periodo</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                @foreach($meses as $mes)
                <tr>
                    <td>{{ $mes['etiqueta'] }}</td>
                    <td>S/ {{ number_format($mes['monto'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ── TOTALES ── --}}
    <div class="totales">
        <div class="fila-total">
            <span>Subtotal ({{ count($meses) }} mes{{ count($meses) > 1 ? 'es' : '' }})</span>
            <span>S/ {{ number_format($subtotal, 2) }}</span>
        </div>
        @if($mora > 0)
        <div class="fila-total mora">
            <span>Mora por atraso</span>
            <span>S/ {{ number_format($mora, 2) }}</span>
        </div>
        @endif
        @if(!empty($fine) && $fine > 0)
        <div class="fila-total mora">
            <span>Multas por falta en asambleas o faenas</span>
            <span>S/ {{ number_format($fine, 2) }}</span>
        </div>
        @endif
        <div class="fila-total total-final">
            <span>TOTAL PAGADO</span>
            <span>S/ {{ number_format($total, 2) }}</span>
        </div>
    </div>

    {{-- ── PIE ── --}}
    <div class="footer">
        @if($jass['tesorero'])
        <div class="firma-linea"></div>
        <div>{{ strtoupper($jass['tesorero']) }}</div>
        <div class="sello">Tesorero / Cajero</div>
        @endif

        @if($jass['presidente'])
        <div style="margin-top: 6px; font-size: 8px; color: #777;">
            Presidente: {{ $jass['presidente'] }}
        </div>
        @endif

        <div style="margin-top: 8px;">
            Gracias por su pago puntual.<br>
            Este recibo es su comprobante oficial.
        </div>
        <div class="sello" style="margin-top: 4px;">
            {{ $jass['nombre'] }} &mdash; {{ date('Y') }}
        </div>
    </div>

</body>

</html>