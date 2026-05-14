<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 landscape;
            margin: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            color: #111;
            background: #fff;
            padding: 0;
            margin: 0;
            padding-left: 2cm;
            padding-top: 1.5cm;
        }

        .page {
            width: 100%;
            height: 100%;
            padding: 0;
        }

        .recibos {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 1.5cm;
            width: calc(100% - 3.5cm);
        }

        .recibo {
            width: 10cm;
            height: 8cm;
            border: 1.5px solid #0b3a66;
            padding: 3mm 4mm;
            border-radius: 2px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #fff;
            overflow: hidden;
        }

        .header {
            text-align: center;
            line-height: 0.8;
            margin-bottom: 1mm;
        }

        .header .title {
            font-size: 6pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .header .subtitle {
            font-size: 5pt;
            color: #0b3a66;
            margin-top: 0.2mm;
            text-transform: uppercase;
        }

        .header .small {
            font-size: 8pt;
            color: #333;
            margin-top: 1mm;
        }

        .top-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 2mm;
            margin-bottom: 1mm;
        }

        .receipt-info {
            flex: 1;
        }

        .receipt-info .headline {
            font-size: 6pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 0.9;
        }

        .receipt-info .subline {
            font-size: 4pt;
            color: #444;
            margin-top: 0.3mm;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        .number-box {
            border: 0.8px solid #111;
            padding: 1.5mm 2mm;
            text-align: right;
            flex-shrink: 0;
        }

        .number-box .label {
            font-size: 4pt;
            color: #444;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        .number-box .value {
            font-size: 8pt;
            font-weight: bold;
            margin-top: 0.3mm;
        }

        .field {
            margin-bottom: 1mm;
        }

        .field .label {
            display: block;
            font-size: 4pt;
            color: #444;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            margin-bottom: 0.2mm;
        }

        .field .value {
            font-size: 5.5pt;
            font-weight: bold;
            line-height: 1.1;
        }

        .field.large .value {
            white-space: pre-wrap;
            font-size: 4.5pt;
        }

        .date-line {
            margin-top: 0.5mm;
            text-align: right;
            font-size: 4pt;
            color: #333;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            gap: 2mm;
            margin-top: 1mm;
        }

        .signature {
            flex: 1;
            text-align: center;
        }

        .signature .line {
            border-top: 0.8px solid #111;
            margin: 0 auto 0.3mm;
            width: 100%;
            height: 1.5mm;
        }

        .signature .label {
            font-size: 3.5pt;
            color: #444;
            text-transform: uppercase;
            letter-spacing: 0.15px;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="recibos">
            @foreach(['TESORERO' => 'TESORERO', 'USUARIO' => 'USUARIO'] as $tipoLabel => $tipoText)
                <div class="recibo">
                    <div>
                        <div class="header">
                            <div class="title">Junta Administradora de Servicios de Saneamiento</div>
                            <div class="subtitle">Del Centro Poblado de Huayoccari</div>
                            <div class="title" style="margin-top:0.5mm;">J.A.S.S.</div>
                        </div>

                        <div class="top-row">
                            <div class="receipt-info">
                                <div class="headline">Recibo de Ingreso<br>Cuota Familiar</div>
                                <div class="subline">N° {{ str_pad($payment->invoice_number, 6, '0', STR_PAD_LEFT) }}</div>
                            </div>
                            <div class="number-box">
                                <div class="label">S/ Pagado</div>
                                <div class="value">{{ number_format($total, 2) }}</div>
                            </div>
                        </div>

                        <div class="field">
                            <span class="label">Recibí del Señor(a):</span>
                            <span class="value">{{ strtoupper($asociado->last_name) }}, {{ strtoupper($asociado->name) }}</span>
                        </div>

                        <div class="field large">
                            <span class="label">La cantidad de:</span>
                            <span class="value">{{ strtoupper($monto_en_letras) }}</span>
                        </div>

                        <div class="field large">
                            <span class="label">Por concepto de:</span>
                            <span class="value">
                                @if(!empty($meses_text))
                                    {{ $meses_text }}
                                @elseif(!empty($payment->concept))
                                    {{ strtoupper($payment->concept) }}
                                @else
                                    {{ strtoupper('Cuota Familiar') }}
                                @endif
                            </span>
                        </div>

                        <div class="date-line">Huayoccari, {{ $fecha_recibo }}</div>
                    </div>

                    <div class="signatures">
                        <div class="signature">
                            <div class="line"></div>
                            <div class="label">Presidente(a)</div>
                        </div>
                        <div class="signature">
                            <div class="line"></div>
                            <div class="label">Tesorero</div>
                        </div>
                    </div>

                    <div style="margin-top:0.5mm; text-align:center; font-size:3.5pt; letter-spacing:0.5px; color:#0b3a66; text-transform:uppercase;">
                        Copia {{ $tipoText }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>