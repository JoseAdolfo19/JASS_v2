<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', Courier, monospace; font-size: 10px; color: #111; background: #fff; width: 226px; padding: 10px 8px; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #555; margin: 6px 0; }
        .divider-solid { border-top: 2px solid #111; margin: 6px 0; }
        .header-title { font-size: 12px; font-weight: bold; text-align: center; letter-spacing: 1px; text-transform: uppercase; }
        .header-sub { font-size: 8px; text-align: center; color: #444; margin-top: 2px; }
        .badge { display: inline-block; background: #dc2626; color: #fff; font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; padding: 2px 7px; border-radius: 20px; margin: 6px auto 2px; }
        .invoice { margin-top: 6px; text-align: center; }
        .invoice .label { font-size: 7px; color: #555; text-transform: uppercase; letter-spacing: 1px; }
        .invoice .number { font-size: 18px; font-weight: bold; letter-spacing: 2px; }
        .field { margin: 4px 0; }
        .field .lbl { font-size: 7px; text-transform: uppercase; color: #555; letter-spacing: 1px; }
        .field .val { font-size: 9px; font-weight: bold; line-height: 1.3; }
        .items-table { width: 100%; border-collapse: collapse; margin: 4px 0; }
        .items-table th { text-align: left; font-size: 7px; color: #555; text-transform: uppercase; padding: 2px 0; border-bottom: 1px solid #ccc; }
        .items-table td { font-size: 9px; padding: 3px 0; vertical-align: top; }
        .items-table td.right { text-align: right; font-weight: bold; }
        .total-row { margin-top: 6px; display: flex; justify-content: space-between; font-weight: bold; font-size: 10px; }
        .firmas { margin-top: 12px; display: flex; justify-content: space-between; gap: 8px; }
        .firma { width: 48%; text-align: center; }
        .firma .line { border-top: 1px solid #111; margin-bottom: 3px; }
        .firma .label { font-size: 7px; color: #555; text-transform: uppercase; letter-spacing: 1px; }
        .footer { margin-top: 10px; font-size: 7px; text-align: center; color: #777; line-height: 1.4; }
    </style>
</head>
<body>
    <div class="center">
        <p class="header-title">Junta Administradora de Servicios de Saneamiento</p>
        <p class="header-sub">Centro Poblado de Huayoccari</p>
        <span class="badge">Multas / Faltas</span>
    </div>

    <div class="invoice center">
        <p class="label">Recibo N°</p>
        <p class="number">{{ $payment->invoice_number }}</p>
    </div>

    <p class="center" style="font-size:7px; color:#444; margin-top:3px;">Emitido: {{ $fecha_emision }}</p>
    <div class="divider"></div>

    <div class="field">
        <p class="lbl">Socio</p>
        <p class="val">{{ strtoupper($asociado->last_name) }}, {{ strtoupper($asociado->name) }}</p>
    </div>
    @if(!empty($asociado->dni))
    <div class="field">
        <p class="lbl">DNI</p>
        <p class="val">{{ $asociado->dni }}</p>
    </div>
    @endif
    @if(!empty($asociado->sector))
    <div class="field">
        <p class="lbl">Sector</p>
        <p class="val">{{ strtoupper($asociado->sector->name) }}</p>
    </div>
    @endif

    <div class="divider"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Evento / Fecha</th>
                <th class="right">S/</th>
            </tr>
        </thead>
        <tbody>
            @foreach($multasDetalle as $multa)
            <tr>
                <td>
                    <div class="val">{{ strtoupper($multa['evento']) }}</div>
                    <div class="lbl">{{ $multa['fecha'] }}</div>
                </td>
                <td class="right">{{ number_format($multa['monto'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider-solid"></div>
    <div class="total-row">
        <span>Total</span>
        <span>S/ {{ number_format($total, 2) }}</span>
    </div>

    <div class="firmas">
        <div class="firma">
            <div class="line"></div>
            <p class="label">Tesorero(a)</p>
        </div>
        <div class="firma">
            <div class="line"></div>
            <p class="label">Firma del Socio</p>
        </div>
    </div>

    <div class="footer">
        <p>Recibo válido como constancia de pago.</p>
        <p>{{ $jass['nombre'] }} - {{ now()->format('Y') }}</p>
    </div>
</body>
</html>
