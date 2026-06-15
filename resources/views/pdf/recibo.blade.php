<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Courier New', Courier, monospace;
    font-size: 8.5pt;
    color: #111;
    background: #fff;
    width: 76mm;
    padding: 5mm 6mm 8mm;
}

/* ── CABECERA ── */
.header { text-align: center; padding-bottom: 3mm; }

.logo-circle {
    width: 14mm;
    height: 14mm;
    border: 2px solid #111;
    border-radius: 50%;
    margin: 0 auto 2mm;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18pt;
    font-weight: 900;
    letter-spacing: -1px;
    line-height: 1;
}

.org-name {
    font-size: 11pt;
    font-weight: 900;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    line-height: 1.2;
}

.org-sub {
    font-size: 7pt;
    color: #555;
    margin-top: 1mm;
    line-height: 1.5;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

/* ── DIVISORES ── */
.div-solid  { border: none; border-top: 1.5px solid #111; margin: 3mm 0; }
.div-dash   { border: none; border-top: 1px dashed #888; margin: 2.5mm 0; }
.div-double {
    border: none;
    border-top: 3px double #111;
    margin: 3mm 0;
}

/* ── ENCABEZADO DEL COMPROBANTE ── */
.doc-header {
    text-align: center;
    margin: 2mm 0;
}
.doc-tipo {
    font-size: 9pt;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 3px;
    border: 1.5px solid #111;
    display: inline-block;
    padding: 1mm 4mm;
}
.doc-numero {
    font-size: 8pt;
    font-weight: 700;
    margin-top: 1.5mm;
    letter-spacing: 1px;
}
.doc-fecha {
    font-size: 7pt;
    color: #666;
    margin-top: 1mm;
}

/* ── DATOS ── */
.section-title {
    font-size: 6.5pt;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #888;
    margin-bottom: 1.5mm;
}

.data-grid {
    width: 100%;
    border-collapse: collapse;
}
.data-grid td {
    font-size: 8pt;
    padding: 0.5mm 0;
    vertical-align: top;
    line-height: 1.5;
}
.data-grid td.lbl { color: #666; width: 22mm; }
.data-grid td.val { font-weight: 700; }

/* ── TABLA DE CONCEPTOS ── */
.items {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1mm;
}
.items thead tr th {
    font-size: 6.5pt;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #666;
    padding: 1mm 0;
    border-bottom: 1px solid #ddd;
}
.items thead tr th:last-child { text-align: right; }

.items tbody tr td {
    font-size: 8pt;
    padding: 1.2mm 0;
    vertical-align: top;
    border-bottom: 1px dotted #eee;
    line-height: 1.4;
}
.items tbody tr:last-child td { border-bottom: none; }
.items tbody tr td:last-child {
    text-align: right;
    font-weight: 700;
    white-space: nowrap;
}

.badge-adelanto {
    font-size: 6pt;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 1px solid #0891b2;
    color: #0891b2;
    padding: 0 1.5mm;
    display: inline-block;
    margin-left: 1mm;
    vertical-align: middle;
}

/* ── TOTALES ── */
.totals { width: 100%; border-collapse: collapse; margin-top: 1mm; }
.totals td { font-size: 8.5pt; padding: 0.7mm 0; vertical-align: middle; }
.totals td:last-child { text-align: right; font-weight: 700; }
.totals .mora-row td { color: #b45309; font-size: 8pt; }
.totals .total-row td {
    font-size: 12pt;
    font-weight: 900;
    padding-top: 2mm;
    letter-spacing: 0.5px;
}
.totals .total-row td:first-child { font-size: 9pt; letter-spacing: 2px; text-transform: uppercase; }

/* ── SELLO ── */
.sello {
    text-align: center;
    margin: 3mm 0;
}
.sello-inner {
    display: inline-block;
    border: 2px solid #111;
    padding: 1.5mm 6mm;
    font-size: 8.5pt;
    font-weight: 900;
    letter-spacing: 4px;
    text-transform: uppercase;
    position: relative;
}

/* ── FIRMAS ── */
.firma-section {
    display: flex;
    gap: 6mm;
    margin-top: 6mm;
}
.firma-box { flex: 1; text-align: center; }
.firma-line {
    border-top: 1px solid #333;
    margin-top: 8mm;
    margin-bottom: 1.5mm;
}
.firma-name {
    font-size: 7pt;
    font-weight: 700;
    text-transform: uppercase;
    line-height: 1.3;
}
.firma-cargo {
    font-size: 6.5pt;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ── PIE ── */
.footer {
    text-align: center;
    margin-top: 4mm;
    font-size: 7pt;
    color: #888;
    line-height: 1.7;
}
.footer .gracias {
    font-size: 8pt;
    font-weight: 900;
    color: #333;
    text-transform: uppercase;
    letter-spacing: 1.5px;
}

/* ── CORTE ── */
.cut-line {
    text-align: center;
    margin: 4mm 0 2mm;
    font-size: 6.5pt;
    color: #bbb;
    letter-spacing: 1px;
}
</style>
</head>
<body>

{{-- ── CABECERA ── --}}
<div class="header">
    <div class="logo-circle">J</div>
    <div class="org-name">{{ $jass['nombre'] }}</div>
    @if(!empty($jass['direccion']))
        <div class="org-sub">{{ $jass['direccion'] }}</div>
    @endif
    <div class="org-sub">Junta Administradora de Servicios<br>de Saneamiento</div>
</div>

<hr class="div-double">

{{-- ── TIPO DE DOCUMENTO ── --}}
<div class="doc-header">
    <div class="doc-tipo">Comprobante de Pago</div>
    <div class="doc-numero">N° {{ str_pad($payment->invoice_number, 8, '0', STR_PAD_LEFT) }}</div>
    <div class="doc-fecha">Emitido el {{ $fecha_emision }}</div>
</div>

<hr class="div-solid">

{{-- ── DATOS DEL SOCIO ── --}}
<div class="section-title">Datos del Socio</div>
<table class="data-grid">
    <tr>
        <td class="lbl">Apellidos y Nombre</td>
        <td class="val">{{ strtoupper($asociado->last_name) }}, {{ strtoupper($asociado->name) }}</td>
    </tr>
    @if(!empty($asociado->dni))
    <tr>
        <td class="lbl">DNI</td>
        <td class="val">{{ $asociado->dni }}</td>
    </tr>
    @endif
    @if($asociado->sector)
    <tr>
        <td class="lbl">Sector</td>
        <td class="val">{{ strtoupper($asociado->sector->nombre ?? $asociado->sector->name ?? '') }}</td>
    </tr>
    @endif
</table>

<hr class="div-dash">

{{-- ── DETALLE DE CUOTAS ── --}}
@if($meses->count() > 0)
<div class="section-title">Detalle — Cuota Familiar</div>
<table class="items">
    <thead>
        <tr>
            <th style="text-align:left">Período</th>
            <th>Importe</th>
        </tr>
    </thead>
    <tbody>
        @foreach($meses as $mes)
        <tr>
            <td>
                {{ $mes['etiqueta'] }}
                @if(isset($mes['adelanto']) && $mes['adelanto'])
                    <span class="badge-adelanto">Adelanto</span>
                @endif
            </td>
            <td>S/ {{ number_format($mes['monto'], 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<hr class="div-dash">
@endif

{{-- ── DETALLE DE MULTAS ── --}}
@if(!empty($multasDetalle) && count($multasDetalle) > 0)
<div class="section-title">Detalle — Multas por Falta</div>
<table class="items">
    <thead>
        <tr>
            <th style="text-align:left">Evento</th>
            <th>Multa</th>
        </tr>
    </thead>
    <tbody>
        @foreach($multasDetalle as $m)
        <tr>
            <td>
                {{ $m['evento'] }}<br>
                <span style="font-size:7pt;color:#888">{{ $m['fecha'] }}</span>
            </td>
            <td>S/ {{ number_format($m['monto'], 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<hr class="div-dash">
@endif

{{-- ── RESUMEN DE TOTALES ── --}}
<table class="totals">
    @if($subtotal > 0)
    <tr>
        <td>Subtotal cuotas</td>
        <td>S/ {{ number_format($subtotal, 2) }}</td>
    </tr>
    @endif
    @if($mora > 0)
    <tr class="mora-row">
        <td>Mora por retraso</td>
        <td>S/ {{ number_format($mora, 2) }}</td>
    </tr>
    @endif
    @if($fine > 0)
    <tr>
        <td>Multas por inasistencia</td>
        <td>S/ {{ number_format($fine, 2) }}</td>
    </tr>
    @endif
    <tr>
        <td colspan="2"><hr class="div-solid" style="margin:1.5mm 0"></td>
    </tr>
    <tr class="total-row">
        <td>TOTAL PAGADO</td>
        <td>S/ {{ number_format($total, 2) }}</td>
    </tr>
</table>

<hr class="div-double">

{{-- ── SELLO CANCELADO ── --}}
<div class="sello">
    <div class="sello-inner">✓ &nbsp; Cancelado</div>
</div>

<hr class="div-dash">

{{-- ── FIRMAS ── --}}
<div class="firma-section">
    <div class="firma-box">
        <div class="firma-line"></div>
        <div class="firma-name">{{ !empty($jass['tesorero']) ? $jass['tesorero'] : '____________________' }}</div>
        <div class="firma-cargo">Tesorero/a</div>
    </div>
    <div class="firma-box">
        <div class="firma-line"></div>
        <div class="firma-name">Firma del Socio</div>
        <div class="firma-cargo">Conforme</div>
    </div>
</div>

{{-- ── PIE ── --}}
<div class="footer">
    <hr class="div-dash" style="margin-top:3mm">
    <div class="gracias">¡Gracias por su pago!</div>
    <div style="margin-top:1mm">Conserve este comprobante como respaldo.</div>
    @if(!empty($jass['presidente']))
        <div style="margin-top:1.5mm">Presidente: {{ $jass['presidente'] }}</div>
    @endif
    <div class="cut-line" style="margin-top:3mm">— — — — — — — — — — — — —</div>
</div>

</body>
</html>