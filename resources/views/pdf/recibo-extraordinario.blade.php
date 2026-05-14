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
        .header-title { font-size: 13px; font-weight: bold; text-align: center; letter-spacing: 1px; text-transform: uppercase; }
        .header-sub { font-size: 8px; text-align: center; color: #444; margin-top: 2px; }
        .badge { display: inline-block; background: #2563eb; color: #fff; font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; padding: 2px 7px; border-radius: 20px; margin: 6px auto 2px; }
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
        .item-name { font-weight: bold; text-transform: uppercase; font-size: 9px; }
        .item-desc { font-size: 7.5px; color: #555; }
        .total-row { display: flex; justify-content: space-between; align-items: baseline; margin-top: 4px; }
        .total-label { font-size: 9px; text-transform: uppercase; color: #333; font-weight: bold; }
        .total-amount { font-size: 20px; font-weight: bold; }
        .firmas { display: flex; justify-content: space-between; margin-top: 14px; gap: 10px; }
        .firma-block { flex: 1; text-align: center; }
        .firma-line { border-top: 1px solid #555; margin-bottom: 3px; }
        .firma-label { font-size: 7.5px; color: #555; text-transform: uppercase; }
        .firma-name { font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .footer { font-size: 7px; color: #777; text-align: center; margin-top: 10px; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="center">
        <p class="header-title">{{ $jass['nombre'] }}</p>
        @if(!empty($jass['direccion']))<p class="header-sub">{{ $jass['direccion'] }}</p>@endif
        <span class="badge">Cuota Extraordinaria</span>
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
                <th>Concepto</th>
                <th class="right">S/</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>
                    <p class="item-name">{{ $item['nombre'] }}</p>
                    @if(!empty($item['descripcion']))<p class="item-desc">{{ $item['descripcion'] }}</p>@endif
                </td>
                <td class="right">{{ number_format($item['monto'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider-solid"></div>
    <div class="total-row">
        <span class="total-label">Total</span>
        <span class="total-amount">S/ {{ number_format($total, 2) }}</span>
    </div>

    <div class="divider" style="margin-top:10px;"></div>
    <div class="firmas">
        <div class="firma-block">
            <div class="firma-line"></div>
            <p class="firma-name">{{ $jass['tesorero'] ?? 'TESORERO(A)' }}</p>
            <p class="firma-label">Tesorero(a)</p>
        </div>
        <div class="firma-block">
            <div class="firma-line"></div>
            <p class="firma-name">{{ strtoupper($asociado->last_name) }}</p>
            <p class="firma-label">Recibí conforme</p>
        </div>
    </div>

    <div class="footer">
        <p>Comprobante válido como constancia de pago.</p>
        <p>{{ $jass['nombre'] }} — {{ now()->format('Y') }}</p>
    </div>
</body>
</html>
