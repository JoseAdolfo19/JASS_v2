<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Comprobante de Pago - Cobros de Agua Potable</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      background: #e8edf2;
      font-family: Arial, Helvetica, sans-serif;
      padding: 20px;
    }

    @media print {
      body { background: white; padding: 0; }
      .page { gap: 0; }
      .comprobante { break-inside: avoid; }
    }

    .page {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      max-width: 800px;
      margin: 0 auto;
    }

    /* ── Comprobante card ── */
    .comprobante {
      background: #ffffff;
      border-radius: 10px;
      border: 1.5px solid #c0c8d8;
      overflow: hidden;
      font-size: 11.5px;
    }

    /* ── Header ── */
    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 12px 6px;
    }

    .logo-area {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .logo-circle {
      width: 46px;
      height: 46px;
      border-radius: 50%;
      border: 2px solid #1a3a8f;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      flex-shrink: 0;
      background: #e6f4fb;
    }

    .title-block {
      display: flex;
      flex-direction: column;
      line-height: 1.15;
    }

    .title-main {
      font-size: 13.5px;
      font-weight: 900;
      color: #1a3a8f;
      letter-spacing: 0.3px;
    }

    .title-sub {
      font-size: 8.5px;
      font-weight: 700;
      color: #1a8fcb;
      letter-spacing: 1px;
      margin-top: 2px;
    }

    /* ── Boleta badge ── */
    .boleta-box {
      background: #1a3a8f;
      color: #fff;
      border-radius: 6px;
      padding: 4px 8px;
      text-align: center;
      min-width: 64px;
      flex-shrink: 0;
    }

    .boleta-label {
      font-size: 7.5px;
      font-weight: 700;
      letter-spacing: 0.8px;
      color: #fff;
    }

    .boleta-num {
      font-size: 14px;
      font-weight: 900;
      color: #e63030;
      background: #fff;
      border-radius: 3px;
      padding: 1px 4px;
      margin-top: 3px;
      display: block;
      letter-spacing: 1px;
    }

    /* ── Wave divider ── */
    .wave {
      height: 6px;
      background: linear-gradient(90deg, #1a3a8f 0%, #1a8fcb 40%, #5bc8f5 70%, #1a3a8f 100%);
      opacity: 0.4;
    }

    /* ── Body ── */
    .body {
      display: flex;
      padding: 10px 12px 6px;
      gap: 8px;
    }

    .fields {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 7px;
    }

    .field-row {
      display: flex;
      align-items: flex-end;
      gap: 4px;
      border-bottom: 1px solid #2a2a2a;
      padding-bottom: 1px;
    }

    .field-row.no-line {
      border-bottom: none;
      align-items: center;
    }

    .field-icon {
      font-size: 12px;
      color: #1a3a8f;
      flex-shrink: 0;
      width: 16px;
      text-align: center;
    }

    .field-label {
      font-size: 8.5px;
      font-weight: 700;
      color: #1a3a8f;
      white-space: nowrap;
      flex-shrink: 0;
    }

    .field-value {
      flex: 1;
      font-size: 10px;
      font-weight: 700;
      color: #111;
      min-height: 14px;
      text-align: right;
      padding-left: 4px;
    }

    /* Periodo DE / A */
    .periodo-row {
      display: flex;
      align-items: flex-end;
      gap: 6px;
      padding-left: 20px;
    }

    .periodo-sub {
      display: flex;
      align-items: flex-end;
      gap: 4px;
      border-bottom: 1px solid #2a2a2a;
      padding-bottom: 1px;
      flex: 1;
    }

    /* ── Water drop illustration ── */
    .drop-wrap {
      flex-shrink: 0;
      width: 62px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* ── Footer area ── */
    .footer-area {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      padding: 8px 12px 6px;
    }

    .gracias {
      font-family: 'Times New Roman', Georgia, serif;
      font-style: italic;
      font-size: 12.5px;
      color: #1a3a8f;
      font-weight: 500;
    }

    .firma-box {
      border: 1px solid #888;
      border-radius: 6px;
      width: 88px;
      height: 44px;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      padding-bottom: 3px;
    }

    .firma-label {
      font-size: 7.5px;
      color: #555;
      text-align: center;
      border-top: 1px solid #888;
      width: 68px;
      padding-top: 2px;
    }

    /* ── Footer bar ── */
    .footer-bar {
      padding: 5px 0;
      text-align: center;
      font-size: 9.5px;
      font-weight: 700;
      letter-spacing: 1.5px;
      color: #ffffff;
    }

    .tesorero .footer-bar { background: #1a3a8f; }
    .cliente  .footer-bar { background: #2a8a2a; }
  </style>
</head>
<body>

@php
    $nombreSocio  = strtoupper($asociado->last_name ?? '') . ', ' . strtoupper($asociado->name ?? '');
    $nroBoleta    = str_pad($payment->invoice_number ?? 0, 6, '0', STR_PAD_LEFT);
    $mesesList    = collect($meses ?? [])->pluck('etiqueta');
    $periodoDesde = $mesesList->first() ?? '—';
    $periodoHasta = $mesesList->last()  ?? '—';

    $icoUser = '<span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#1a3a8f;color:#fff;font-size:8px;font-weight:900;text-align:center;line-height:12px;">U</span>';
    $icoDollar = '<span style="color:#1a3a8f;font-weight:900;font-size:13px;">$</span>';
    $icoCalendar = '<span style="display:inline-block;width:12px;height:12px;border-radius:2px;background:#1a3a8f;color:#fff;font-size:8px;font-weight:900;text-align:center;line-height:12px;">D</span>';
@endphp

<div class="page">

  <!-- ══════════════════════════════════
       COMPROBANTE 1 — TESORERO
  ══════════════════════════════════ -->
  <div class="comprobante tesorero">
    <div class="header">
      <div class="logo-area">
        <div class="logo-circle">
          <svg viewBox="0 0 46 46" width="46" height="46" xmlns="http://www.w3.org/2000/svg">
            <circle cx="23" cy="23" r="22" fill="#e6f4fb"/>
            <ellipse cx="23" cy="32" rx="14" ry="5" fill="#5bc8f5" opacity="0.5"/>
            <path d="M23 8 Q28 18 28 26 A5 5 0 0 1 18 26 Q18 18 23 8Z" fill="#1a8fcb"/>
            <path d="M23 11 Q25 19 25 26 A2 2 0 0 1 21 26 Q21 19 23 11Z" fill="white" opacity="0.35"/>
            <ellipse cx="23" cy="36" rx="10" ry="3" fill="#1a8fcb" opacity="0.2"/>
          </svg>
        </div>
        <div class="title-block">
          <span class="title-main">{{ $jass['nombre'] ?? 'COMPROBANTE DE PAGO' }}</span>
          <span class="title-sub">COBROS DE AGUA POTABLE</span>
        </div>
      </div>
      <div class="boleta-box">
        <div class="boleta-label">N° BOLETA</div>
        <span class="boleta-num">{{ $nroBoleta }}</span>
      </div>
    </div>
    <div class="wave"></div>
    <div class="body">
      <div class="fields">
        <div class="field-row">
          <span class="field-icon">{!! $icoUser !!}</span>
          <span class="field-label">NOMBRE DEL PAGADOR:</span>
          <span class="field-value">{{ $nombreSocio }}</span>
        </div>
        <div class="field-row">
          <span class="field-icon">{!! $icoDollar !!}</span>
          <span class="field-label">MONTO PAGADO (S/):</span>
          <span class="field-value">S/ {{ number_format($total, 2) }}</span>
        </div>
        <div class="field-row no-line">
          <span class="field-icon">{!! $icoCalendar !!}</span>
          <span class="field-label">PERIODO PAGADO:</span>
        </div>
        <div class="periodo-row">
          <div class="periodo-sub">
            <span class="field-label">DE:</span>
            <span class="field-value">{{ $periodoDesde }}</span>
          </div>
          <div class="periodo-sub">
            <span class="field-label">A:</span>
            <span class="field-value">{{ $periodoHasta }}</span>
          </div>
        </div>
        <div class="field-row">
          <span class="field-icon">{!! $icoCalendar !!}</span>
          <span class="field-label">FECHA DE PAGO:</span>
          <span class="field-value">{{ $fecha_emision ?? now()->format('d/m/Y H:i') }}</span>
        </div>
      </div>
      <div class="drop-wrap">
        <svg viewBox="0 0 62 85" width="62" height="85" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <radialGradient id="drop-a" cx="38%" cy="28%" r="65%">
              <stop offset="0%" stop-color="#ceeefb"/>
              <stop offset="100%" stop-color="#1a8fcb"/>
            </radialGradient>
          </defs>
          <path d="M31 6 Q47 32 47 52 A16 16 0 0 1 15 52 Q15 32 31 6Z" fill="url(#drop-a)" stroke="#1a3a8f" stroke-width="0.8"/>
          <path d="M25 28 Q27 22 34 25" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round" opacity="0.55"/>
          <ellipse cx="31" cy="70" rx="18" ry="6" fill="#5bc8f5" opacity="0.4"/>
          <ellipse cx="14" cy="73" rx="8" ry="3" fill="#5bc8f5" opacity="0.25"/>
          <ellipse cx="48" cy="73" rx="8" ry="3" fill="#5bc8f5" opacity="0.25"/>
        </svg>
      </div>
    </div>
    <div class="footer-area">
      <div class="gracias">¡Gracias por su pago!</div>
      <div class="firma-box">
        <div class="firma-label">Firma y Sello</div>
      </div>
    </div>
    <div class="footer-bar">COPIA PARA EL TESORERO</div>
  </div>

  <!-- ══════════════════════════════════
       COMPROBANTE 2 — TESORERO
  ══════════════════════════════════ -->
  <div class="comprobante tesorero">
    <div class="header">
      <div class="logo-area">
        <div class="logo-circle">
          <svg viewBox="0 0 46 46" width="46" height="46" xmlns="http://www.w3.org/2000/svg">
            <circle cx="23" cy="23" r="22" fill="#e6f4fb"/>
            <ellipse cx="23" cy="32" rx="14" ry="5" fill="#5bc8f5" opacity="0.5"/>
            <path d="M23 8 Q28 18 28 26 A5 5 0 0 1 18 26 Q18 18 23 8Z" fill="#1a8fcb"/>
            <path d="M23 11 Q25 19 25 26 A2 2 0 0 1 21 26 Q21 19 23 11Z" fill="white" opacity="0.35"/>
            <ellipse cx="23" cy="36" rx="10" ry="3" fill="#1a8fcb" opacity="0.2"/>
          </svg>
        </div>
        <div class="title-block">
          <span class="title-main">{{ $jass['nombre'] ?? 'COMPROBANTE DE PAGO' }}</span>
          <span class="title-sub">COBROS DE AGUA POTABLE</span>
        </div>
      </div>
      <div class="boleta-box">
        <div class="boleta-label">N° BOLETA</div>
        <span class="boleta-num">{{ $nroBoleta }}</span>
      </div>
    </div>
    <div class="wave"></div>
    <div class="body">
      <div class="fields">
        <div class="field-row">
          <span class="field-icon">{!! $icoUser !!}</span>
          <span class="field-label">NOMBRE DEL PAGADOR:</span>
          <span class="field-value">{{ $nombreSocio }}</span>
        </div>
        <div class="field-row">
          <span class="field-icon">{!! $icoDollar !!}</span>
          <span class="field-label">MONTO PAGADO (S/):</span>
          <span class="field-value">S/ {{ number_format($total, 2) }}</span>
        </div>
        <div class="field-row no-line">
          <span class="field-icon">{!! $icoCalendar !!}</span>
          <span class="field-label">PERIODO PAGADO:</span>
        </div>
        <div class="periodo-row">
          <div class="periodo-sub">
            <span class="field-label">DE:</span>
            <span class="field-value">{{ $periodoDesde }}</span>
          </div>
          <div class="periodo-sub">
            <span class="field-label">A:</span>
            <span class="field-value">{{ $periodoHasta }}</span>
          </div>
        </div>
        <div class="field-row">
          <span class="field-icon">{!! $icoCalendar !!}</span>
          <span class="field-label">FECHA DE PAGO:</span>
          <span class="field-value">{{ $fecha_emision ?? now()->format('d/m/Y H:i') }}</span>
        </div>
      </div>
      <div class="drop-wrap">
        <svg viewBox="0 0 62 85" width="62" height="85" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <radialGradient id="drop-b" cx="38%" cy="28%" r="65%">
              <stop offset="0%" stop-color="#ceeefb"/>
              <stop offset="100%" stop-color="#1a8fcb"/>
            </radialGradient>
          </defs>
          <path d="M31 6 Q47 32 47 52 A16 16 0 0 1 15 52 Q15 32 31 6Z" fill="url(#drop-b)" stroke="#1a3a8f" stroke-width="0.8"/>
          <path d="M25 28 Q27 22 34 25" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round" opacity="0.55"/>
          <ellipse cx="31" cy="70" rx="18" ry="6" fill="#5bc8f5" opacity="0.4"/>
          <ellipse cx="14" cy="73" rx="8" ry="3" fill="#5bc8f5" opacity="0.25"/>
          <ellipse cx="48" cy="73" rx="8" ry="3" fill="#5bc8f5" opacity="0.25"/>
        </svg>
      </div>
    </div>
    <div class="footer-area">
      <div class="gracias">¡Gracias por su pago!</div>
      <div class="firma-box">
        <div class="firma-label">Firma y Sello</div>
      </div>
    </div>
    <div class="footer-bar">COPIA PARA EL TESORERO</div>
  </div>

  <!-- ══════════════════════════════════
       COMPROBANTE 3 — CLIENTE
  ══════════════════════════════════ -->
  <div class="comprobante cliente">
    <div class="header">
      <div class="logo-area">
        <div class="logo-circle">
          <svg viewBox="0 0 46 46" width="46" height="46" xmlns="http://www.w3.org/2000/svg">
            <circle cx="23" cy="23" r="22" fill="#e6f4fb"/>
            <ellipse cx="23" cy="32" rx="14" ry="5" fill="#5bc8f5" opacity="0.5"/>
            <path d="M23 8 Q28 18 28 26 A5 5 0 0 1 18 26 Q18 18 23 8Z" fill="#1a8fcb"/>
            <path d="M23 11 Q25 19 25 26 A2 2 0 0 1 21 26 Q21 19 23 11Z" fill="white" opacity="0.35"/>
            <ellipse cx="23" cy="36" rx="10" ry="3" fill="#1a8fcb" opacity="0.2"/>
          </svg>
        </div>
        <div class="title-block">
          <span class="title-main">{{ $jass['nombre'] ?? 'COMPROBANTE DE PAGO' }}</span>
          <span class="title-sub">COBROS DE AGUA POTABLE</span>
        </div>
      </div>
      <div class="boleta-box">
        <div class="boleta-label">N° BOLETA</div>
        <span class="boleta-num">{{ $nroBoleta }}</span>
      </div>
    </div>
    <div class="wave"></div>
    <div class="body">
      <div class="fields">
        <div class="field-row">
          <span class="field-icon">{!! $icoUser !!}</span>
          <span class="field-label">NOMBRE DEL PAGADOR:</span>
          <span class="field-value">{{ $nombreSocio }}</span>
        </div>
        <div class="field-row">
          <span class="field-icon">{!! $icoDollar !!}</span>
          <span class="field-label">MONTO PAGADO (S/):</span>
          <span class="field-value">S/ {{ number_format($total, 2) }}</span>
        </div>
        <div class="field-row no-line">
          <span class="field-icon">{!! $icoCalendar !!}</span>
          <span class="field-label">PERIODO PAGADO:</span>
        </div>
        <div class="periodo-row">
          <div class="periodo-sub">
            <span class="field-label">DE:</span>
            <span class="field-value">{{ $periodoDesde }}</span>
          </div>
          <div class="periodo-sub">
            <span class="field-label">A:</span>
            <span class="field-value">{{ $periodoHasta }}</span>
          </div>
        </div>
        <div class="field-row">
          <span class="field-icon">{!! $icoCalendar !!}</span>
          <span class="field-label">FECHA DE PAGO:</span>
          <span class="field-value">{{ $fecha_emision ?? now()->format('d/m/Y H:i') }}</span>
        </div>
      </div>
      <div class="drop-wrap">
        <svg viewBox="0 0 62 85" width="62" height="85" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <radialGradient id="drop-c" cx="38%" cy="28%" r="65%">
              <stop offset="0%" stop-color="#ceeefb"/>
              <stop offset="100%" stop-color="#1a8fcb"/>
            </radialGradient>
          </defs>
          <path d="M31 6 Q47 32 47 52 A16 16 0 0 1 15 52 Q15 32 31 6Z" fill="url(#drop-c)" stroke="#1a3a8f" stroke-width="0.8"/>
          <path d="M25 28 Q27 22 34 25" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round" opacity="0.55"/>
          <ellipse cx="31" cy="70" rx="18" ry="6" fill="#5bc8f5" opacity="0.4"/>
          <ellipse cx="14" cy="73" rx="8" ry="3" fill="#5bc8f5" opacity="0.25"/>
          <ellipse cx="48" cy="73" rx="8" ry="3" fill="#5bc8f5" opacity="0.25"/>
        </svg>
      </div>
    </div>
    <div class="footer-area">
      <div class="gracias">¡Gracias por su pago!</div>
      <div class="firma-box">
        <div class="firma-label">Firma y Sello</div>
      </div>
    </div>
    <div class="footer-bar">COPIA PARA EL CLIENTE</div>
  </div>

  <!-- ══════════════════════════════════
       COMPROBANTE 4 — CLIENTE
  ══════════════════════════════════ -->
  <div class="comprobante cliente">
    <div class="header">
      <div class="logo-area">
        <div class="logo-circle">
          <svg viewBox="0 0 46 46" width="46" height="46" xmlns="http://www.w3.org/2000/svg">
            <circle cx="23" cy="23" r="22" fill="#e6f4fb"/>
            <ellipse cx="23" cy="32" rx="14" ry="5" fill="#5bc8f5" opacity="0.5"/>
            <path d="M23 8 Q28 18 28 26 A5 5 0 0 1 18 26 Q18 18 23 8Z" fill="#1a8fcb"/>
            <path d="M23 11 Q25 19 25 26 A2 2 0 0 1 21 26 Q21 19 23 11Z" fill="white" opacity="0.35"/>
            <ellipse cx="23" cy="36" rx="10" ry="3" fill="#1a8fcb" opacity="0.2"/>
          </svg>
        </div>
        <div class="title-block">
          <span class="title-main">{{ $jass['nombre'] ?? 'COMPROBANTE DE PAGO' }}</span>
          <span class="title-sub">COBROS DE AGUA POTABLE</span>
        </div>
      </div>
      <div class="boleta-box">
        <div class="boleta-label">N° BOLETA</div>
        <span class="boleta-num">{{ $nroBoleta }}</span>
      </div>
    </div>
    <div class="wave"></div>
    <div class="body">
      <div class="fields">
        <div class="field-row">
          <span class="field-icon">{!! $icoUser !!}</span>
          <span class="field-label">NOMBRE DEL PAGADOR:</span>
          <span class="field-value">{{ $nombreSocio }}</span>
        </div>
        <div class="field-row">
          <span class="field-icon">{!! $icoDollar !!}</span>
          <span class="field-label">MONTO PAGADO (S/):</span>
          <span class="field-value">S/ {{ number_format($total, 2) }}</span>
        </div>
        <div class="field-row no-line">
          <span class="field-icon">{!! $icoCalendar !!}</span>
          <span class="field-label">PERIODO PAGADO:</span>
        </div>
        <div class="periodo-row">
          <div class="periodo-sub">
            <span class="field-label">DE:</span>
            <span class="field-value">{{ $periodoDesde }}</span>
          </div>
          <div class="periodo-sub">
            <span class="field-label">A:</span>
            <span class="field-value">{{ $periodoHasta }}</span>
          </div>
        </div>
        <div class="field-row">
          <span class="field-icon">{!! $icoCalendar !!}</span>
          <span class="field-label">FECHA DE PAGO:</span>
          <span class="field-value">{{ $fecha_emision ?? now()->format('d/m/Y H:i') }}</span>
        </div>
      </div>
      <div class="drop-wrap">
        <svg viewBox="0 0 62 85" width="62" height="85" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <radialGradient id="drop-d" cx="38%" cy="28%" r="65%">
              <stop offset="0%" stop-color="#ceeefb"/>
              <stop offset="100%" stop-color="#1a8fcb"/>
            </radialGradient>
          </defs>
          <path d="M31 6 Q47 32 47 52 A16 16 0 0 1 15 52 Q15 32 31 6Z" fill="url(#drop-d)" stroke="#1a3a8f" stroke-width="0.8"/>
          <path d="M25 28 Q27 22 34 25" stroke="white" stroke-width="1.5" fill="none" stroke-linecap="round" opacity="0.55"/>
          <ellipse cx="31" cy="70" rx="18" ry="6" fill="#5bc8f5" opacity="0.4"/>
          <ellipse cx="14" cy="73" rx="8" ry="3" fill="#5bc8f5" opacity="0.25"/>
          <ellipse cx="48" cy="73" rx="8" ry="3" fill="#5bc8f5" opacity="0.25"/>
        </svg>
      </div>
    </div>
    <div class="footer-area">
      <div class="gracias">¡Gracias por su pago!</div>
      <div class="firma-box">
        <div class="firma-label">Firma y Sello</div>
      </div>
    </div>
    <div class="footer-bar">COPIA PARA EL CLIENTE</div>
  </div>

</div>
</body>
</html>
