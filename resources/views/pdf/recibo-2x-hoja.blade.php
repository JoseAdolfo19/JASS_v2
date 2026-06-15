cat > /mnt/user-data/outputs/recibo-2x-hoja_blade.php << 'BLADE_EOF'
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }

body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 9pt;
    color: #111;
    background: #fff;
    width: 210mm;
    height: 297mm;
    padding: 6mm 8mm;
}

/* Cada boleta ocupa media hoja A4 */
.boleta {
    width: 100%;
    height: 130mm;
    border: 1.5px solid #ccc;
    border-radius: 4px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    padding: 4mm 5mm 3mm;
    page-break-inside: avoid;
}

.boleta + .boleta {
    margin-top: 4mm;
}

/* Separador entre boletas de la misma hoja */
.corte-hoja {
    border-top: 1.5px dashed #bbb;
    margin: 2mm 0;
    text-align: center;
    font-size: 6.5pt;
    color: #bbb;
    letter-spacing: 1px;
}

/* ══ CABECERA ══ */
.header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5mm;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 3mm;
    flex: 1;
}

.logo-wrap { flex-shrink:0; width:16mm; height:16mm; }

.header-titles { flex:1; }

.titulo-principal {
    font-size: 13pt;
    font-weight: 900;
    color: #1a3a7c;
    text-transform: uppercase;
    letter-spacing: 1px;
    line-height: 1.1;
}

.subtitulo {
    font-size: 8pt;
    color: #1a7abf;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 0.5mm;
}

.org-name {
    font-size: 7pt;
    color: #555;
    margin-top: 0.5mm;
}

.badge-numero {
    flex-shrink: 0;
    border: 2.5px solid #1a3a7c;
    border-radius: 5px;
    text-align: center;
    padding: 1.5mm 3mm;
    min-width: 22mm;
}
.badge-label {
    font-size: 7pt;
    font-weight: 900;
    color: #1a3a7c;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.badge-num {
    font-size: 16pt;
    font-weight: 900;
    color: #e53e3e;
    letter-spacing: 1.5px;
    line-height: 1.1;
}

.blue-line {
    height: 3px;
    background: linear-gradient(90deg, #1a3a7c 0%, #1a7abf 60%, #fff 100%);
    margin: 1.5mm 0;
    border-radius: 2px;
}

/* ══ CUERPO ══ */
.body-grid {
    display: flex;
    gap: 3mm;
    flex: 1;
    margin-top: 1.5mm;
}

.body-fields { flex: 1; }

.gota-lado {
    flex-shrink: 0;
    width: 28mm;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Campos en 2 columnas */
.campos-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2mm 4mm;
}

.campo-full { grid-column: 1 / -1; }

.campo { display: flex; align-items: flex-start; gap: 2mm; }

.campo-icono { width: 5mm; flex-shrink:0; margin-top:0.3mm; }

.campo-contenido { flex:1; min-width:0; }

.campo-label {
    font-size: 7.5pt;
    font-weight: 900;
    color: #1a3a7c;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    display: block;
    margin-bottom: 0.5mm;
}

.campo-valor {
    font-size: 9pt;
    font-weight: 700;
    color: #111;
    border-bottom: 1px solid #999;
    display: block;
    padding-bottom: 0.5mm;
    min-height: 4.5mm;
}

.campo-valor-sm {
    font-size: 8pt;
    font-weight: 700;
    color: #111;
    border-bottom: 1px solid #999;
    display: inline-block;
    padding-bottom: 0.5mm;
    min-height: 4.5mm;
    min-width: 28mm;
}

.periodo-fila { display:flex; gap:4mm; align-items:center; margin-top:0.5mm; }
.periodo-parte { display:flex; align-items:center; gap:1.5mm; }
.periodo-txt { font-size: 7.5pt; font-weight:900; color:#1a3a7c; }

/* ══ PIE ══ */
.pie-grid {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-top: 2mm;
}

.gracias-txt {
    font-family: Georgia, 'Times New Roman', serif;
    font-style: italic;
    font-size: 10pt;
    color: #1a3a7c;
    font-weight: 700;
}

.firma-box {
    border: 1px solid #aaa;
    border-radius: 4px;
    width: 30mm;
    padding: 1.5mm;
    text-align: center;
    min-height: 14mm;
}
.firma-linea { border-top:1px solid #555; margin:8mm 3mm 1.5mm; }
.firma-label { font-size:7pt; color:#555; text-transform:uppercase; font-weight:700; }

/* Barra pie */
.barra-pie {
    margin-top: 2mm;
    padding: 2.5mm 4mm;
    text-align: center;
    font-size: 9pt;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #fff;
    border-radius: 3px;
}
.barra-tesorero { background: #1a3a7c; }
.barra-cliente  { background: #2d7d32; }
</style>
</head>
<body>

@php
    $nombreSocio  = strtoupper($asociado->last_name) . ', ' . strtoupper($asociado->name);
    $nroBoleta    = str_pad($payment->invoice_number, 6, '0', STR_PAD_LEFT);
    $mesesList    = collect($meses)->pluck('etiqueta');
    $periodoDesde = $mesesList->first() ?? '—';
    $periodoHasta = $mesesList->last()  ?? '—';

    $svgGota = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 110" width="100%" height="100%">
        <defs>
            <radialGradient id="g2" cx="40%" cy="35%">
                <stop offset="0%" stop-color="#b3e5fc"/>
                <stop offset="60%" stop-color="#1a7abf"/>
                <stop offset="100%" stop-color="#0d47a1"/>
            </radialGradient>
        </defs>
        <path d="M40 5 C40 5 8 50 8 68 a32 32 0 0 0 64 0 C72 50 40 5 40 5Z"
              fill="url(#g2)" stroke="#0d47a1" stroke-width="1.5"/>
        <ellipse cx="28" cy="48" rx="7" ry="11" fill="rgba(255,255,255,0.35)" transform="rotate(-20,28,48)"/>
        <ellipse cx="40" cy="102" rx="30" ry="6" fill="#1a7abf" opacity="0.3"/>
        <ellipse cx="40" cy="107" rx="22" ry="4" fill="#1a7abf" opacity="0.2"/>
    </svg>';

    $svgLogo = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" width="100%" height="100%">
        <circle cx="40" cy="40" r="38" fill="#1a3a7c" stroke="#0d2a6b" stroke-width="2"/>
        <path d="M40 12 C40 12 20 40 20 52 a20 20 0 0 0 40 0 C60 40 40 12 40 12Z"
              fill="#b3e5fc" stroke="#fff" stroke-width="1"/>
        <path d="M40 18 C40 18 24 42 24 52 a16 16 0 0 0 32 0 C56 42 40 18 40 18Z"
              fill="#1a7abf"/>
        <ellipse cx="33" cy="43" rx="4" ry="7" fill="rgba(255,255,255,0.4)" transform="rotate(-20,33,43)"/>
        <path d="M10 68 Q25 62 40 68 Q55 74 70 68 L70 76 Q55 82 40 76 Q25 70 10 76Z"
              fill="#1a7abf" opacity="0.5"/>
    </svg>';

    /* Iconos SVG reutilizables */
    $icoPersona   = '<svg viewBox="0 0 24 24" width="15" height="15" fill="#1a3a7c"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>';
    $icoDolar     = '<svg viewBox="0 0 24 24" width="15" height="15" fill="#1a3a7c"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>';
    $icoCalendario = '<svg viewBox="0 0 24 24" width="15" height="15" fill="#1a3a7c"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>';
@endphp

{{-- ══ BOLETA 1: TESORERO ══ --}}
<div class="boleta">
    <div class="header">
        <div class="header-left">
            <div class="logo-wrap">{!! $svgLogo !!}</div>
            <div class="header-titles">
                <div class="titulo-principal">Comprobante de Pago</div>
                <div class="subtitulo">💧 Cobros de Agua Potable 💧</div>
                <div class="org-name">{{ $jass['nombre'] }}</div>
            </div>
        </div>
        <div class="badge-numero">
            <div class="badge-label">N° Boleta</div>
            <div class="badge-num">{{ $nroBoleta }}</div>
        </div>
    </div>

    <div class="blue-line"></div>

    <div class="body-grid">
        <div class="body-fields">
            <div class="campos-grid">

                <div class="campo campo-full">
                    <div class="campo-icono">{!! $icoPersona !!}</div>
                    <div class="campo-contenido">
                        <span class="campo-label">Nombre del Pagador:</span>
                        <span class="campo-valor">{{ $nombreSocio }}</span>
                    </div>
                </div>

                <div class="campo campo-full">
                    <div class="campo-icono">{!! $icoDolar !!}</div>
                    <div class="campo-contenido">
                        <span class="campo-label">Monto Pagado (S/):</span>
                        <span class="campo-valor">S/ {{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <div class="campo campo-full">
                    <div class="campo-icono">{!! $icoCalendario !!}</div>
                    <div class="campo-contenido">
                        <span class="campo-label">Período Pagado:</span>
                        <div class="periodo-fila">
                            <div class="periodo-parte">
                                <span class="periodo-txt">De:</span>
                                <span class="campo-valor-sm">{{ $periodoDesde }}</span>
                            </div>
                            <div class="periodo-parte">
                                <span class="periodo-txt">A:</span>
                                <span class="campo-valor-sm">{{ $periodoHasta }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="campo campo-full">
                    <div class="campo-icono">{!! $icoCalendario !!}</div>
                    <div class="campo-contenido">
                        <span class="campo-label">Fecha de Pago:</span>
                        <span class="campo-valor">{{ $fecha_emision }}</span>
                    </div>
                </div>

            </div>
        </div>
        <div class="gota-lado">
            <div style="width:26mm;height:36mm;">{!! $svgGota !!}</div>
        </div>
    </div>

    <div class="pie-grid">
        <div class="gracias-txt">¡Gracias por su pago! 💧</div>
        <div class="firma-box">
            <div class="firma-linea"></div>
            <div class="firma-label">Firma y Sello</div>
        </div>
    </div>

    <div class="barra-pie barra-tesorero">Copia para el Tesorero</div>
</div>

<div class="corte-hoja">— — — — — — — — — — — — — — — — — — — — — — — —</div>

{{-- ══ BOLETA 2: CLIENTE ══ --}}
<div class="boleta">
    <div class="header">
        <div class="header-left">
            <div class="logo-wrap">{!! $svgLogo !!}</div>
            <div class="header-titles">
                <div class="titulo-principal">Comprobante de Pago</div>
                <div class="subtitulo">💧 Cobros de Agua Potable 💧</div>
                <div class="org-name">{{ $jass['nombre'] }}</div>
            </div>
        </div>
        <div class="badge-numero">
            <div class="badge-label">N° Boleta</div>
            <div class="badge-num">{{ $nroBoleta }}</div>
        </div>
    </div>

    <div class="blue-line"></div>

    <div class="body-grid">
        <div class="body-fields">
            <div class="campos-grid">

                <div class="campo campo-full">
                    <div class="campo-icono">{!! $icoPersona !!}</div>
                    <div class="campo-contenido">
                        <span class="campo-label">Nombre del Pagador:</span>
                        <span class="campo-valor">{{ $nombreSocio }}</span>
                    </div>
                </div>

                <div class="campo campo-full">
                    <div class="campo-icono">{!! $icoDolar !!}</div>
                    <div class="campo-contenido">
                        <span class="campo-label">Monto Pagado (S/):</span>
                        <span class="campo-valor">S/ {{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <div class="campo campo-full">
                    <div class="campo-icono">{!! $icoCalendario !!}</div>
                    <div class="campo-contenido">
                        <span class="campo-label">Período Pagado:</span>
                        <div class="periodo-fila">
                            <div class="periodo-parte">
                                <span class="periodo-txt">De:</span>
                                <span class="campo-valor-sm">{{ $periodoDesde }}</span>
                            </div>
                            <div class="periodo-parte">
                                <span class="periodo-txt">A:</span>
                                <span class="campo-valor-sm">{{ $periodoHasta }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="campo campo-full">
                    <div class="campo-icono">{!! $icoCalendario !!}</div>
                    <div class="campo-contenido">
                        <span class="campo-label">Fecha de Pago:</span>
                        <span class="campo-valor">{{ $fecha_emision }}</span>
                    </div>
                </div>

            </div>
        </div>
        <div class="gota-lado">
            <div style="width:26mm;height:36mm;">{!! $svgGota !!}</div>
        </div>
    </div>

    <div class="pie-grid">
        <div class="gracias-txt">¡Gracias por su pago! 💧</div>
        <div class="firma-box">
            <div class="firma-linea"></div>
            <div class="firma-label">Firma y Sello</div>
        </div>
    </div>

    <div class="barra-pie barra-cliente">Copia para el Cliente</div>
</div>

</body>
</html>