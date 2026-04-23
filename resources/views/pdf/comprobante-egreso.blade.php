<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Egreso</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; padding: 20px; }

        .header { text-align: center; border-bottom: 3px solid #1e3a5f; padding-bottom: 14px; margin-bottom: 20px; }
        .header .jass { font-size: 18px; font-weight: bold; text-transform: uppercase; color: #1e3a5f; }
        .header .sub  { font-size: 11px; color: #555; margin-top: 2px; }
        .header .titulo { font-size: 15px; font-weight: bold; margin-top: 10px; text-transform: uppercase;
                          border: 2px solid #1e3a5f; display: inline-block; padding: 4px 20px; border-radius: 6px; }

        .meta { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .meta-box { border: 1px solid #ddd; padding: 10px 14px; border-radius: 8px; min-width: 180px; }
        .meta-box .lbl { font-size: 9px; text-transform: uppercase; color: #888; margin-bottom: 3px; }
        .meta-box .val { font-weight: bold; font-size: 13px; color: #111; }

        .seccion { margin-bottom: 18px; }
        .seccion-titulo { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #888;
                          border-bottom: 1px solid #eee; padding-bottom: 4px; margin-bottom: 10px; }

        .grid-2 { display: flex; gap: 16px; }
        .grid-2 > div { flex: 1; }

        .campo { margin-bottom: 10px; }
        .campo .lbl { font-size: 9px; text-transform: uppercase; color: #888; margin-bottom: 2px; }
        .campo .val { font-size: 12px; font-weight: bold; color: #111;
                      border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        .campo .val.vacio { color: #aaa; font-weight: normal; font-style: italic; }

        .monto-box { background: #f0f7ff; border: 2px solid #1e3a5f; border-radius: 10px;
                     padding: 16px; text-align: center; margin: 20px 0; }
        .monto-box .lbl { font-size: 10px; text-transform: uppercase; color: #555; margin-bottom: 4px; }
        .monto-box .monto { font-size: 32px; font-weight: bold; color: #1e3a5f; }

        .badge { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 10px;
                 font-weight: bold; text-transform: uppercase; }
        .badge-boleta            { background: #dbeafe; color: #1d4ed8; }
        .badge-factura           { background: #ede9fe; color: #6d28d9; }
        .badge-recibo_honorarios { background: #dcfce7; color: #15803d; }
        .badge-declaracion_jurada{ background: #ffedd5; color: #c2410c; }
        .badge-otro              { background: #f4f4f5; color: #52525b; }

        .notas { background: #fafafa; border: 1px dashed #ddd; border-radius: 8px;
                 padding: 10px; font-size: 11px; color: #444; margin-bottom: 18px; }

        .firmas { display: flex; justify-content: space-around; margin-top: 40px; }
        .firma { text-align: center; width: 180px; }
        .firma .linea { border-top: 1px solid #000; margin-bottom: 6px; }
        .firma .nombre { font-size: 11px; font-weight: bold; }
        .firma .cargo  { font-size: 9px; color: #888; text-transform: uppercase; }

        .footer { margin-top: 24px; text-align: center; font-size: 9px; color: #aaa;
                  border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>

    {{-- CABECERA --}}
    <div class="header">
        <div class="jass">{{ $jass['nombre'] }}</div>
        @if($jass['direccion'])
        <div class="sub">{{ $jass['direccion'] }}</div>
        @endif
        <div class="titulo">Comprobante de Egreso</div>
    </div>

    {{-- META: tipo + fecha --}}
    <div class="meta">
        <div class="meta-box">
            <div class="lbl">Tipo de Comprobante</div>
            <div class="val">
                <span class="badge badge-{{ $expense->voucher_type }}">
                    {{ $expense->voucher_type_label }}
                </span>
            </div>
        </div>
        <div class="meta-box">
            <div class="lbl">N° Comprobante</div>
            <div class="val">{{ $expense->voucher_number ?: '—' }}</div>
        </div>
        <div class="meta-box">
            <div class="lbl">Fecha</div>
            <div class="val">{{ $expense->date->format('d/m/Y') }}</div>
        </div>
        <div class="meta-box">
            <div class="lbl">Categoría</div>
            <div class="val">{{ $expense->category }}</div>
        </div>
    </div>

    {{-- MONTO --}}
    <div class="monto-box">
        <div class="lbl">Monto Total del Egreso</div>
        <div class="monto">S/ {{ number_format($expense->amount, 2) }}</div>
    </div>

    {{-- DATOS DEL PROVEEDOR / BENEFICIARIO --}}
    <div class="seccion">
        <div class="seccion-titulo">
            @if($expense->voucher_type === 'recibo_honorarios') Datos del Beneficiario
            @elseif($expense->voucher_type === 'declaracion_jurada') Datos del Declarante
            @else Datos del Proveedor
            @endif
        </div>
        <div class="grid-2">
            <div class="campo">
                <div class="lbl">
                    @if(in_array($expense->voucher_type, ['boleta','factura'])) Razón Social / Empresa
                    @else Nombre Completo
                    @endif
                </div>
                <div class="val {{ $expense->beneficiary ? '' : 'vacio' }}">
                    {{ $expense->beneficiary ?: 'No especificado' }}
                </div>
            </div>
            <div class="campo">
                <div class="lbl">
                    {{ in_array($expense->voucher_type, ['boleta','factura']) ? 'RUC' : 'DNI' }}
                </div>
                <div class="val {{ $expense->ruc_dni ? '' : 'vacio' }}">
                    {{ $expense->ruc_dni ?: 'No especificado' }}
                </div>
            </div>
        </div>
    </div>

    {{-- DESCRIPCIÓN --}}
    <div class="seccion">
        <div class="seccion-titulo">Descripción del Gasto</div>
        <div class="campo">
            <div class="val">{{ $expense->description }}</div>
        </div>
    </div>

    {{-- OBSERVACIONES --}}
    @if($expense->notes)
    <div class="seccion">
        <div class="seccion-titulo">Observaciones / Detalle Adicional</div>
        <div class="notas">{{ $expense->notes }}</div>
    </div>
    @endif

    {{-- FIRMAS --}}
    <div class="firmas">
        <div class="firma">
            <div class="linea"></div>
            <div class="nombre">{{ $expense->beneficiary ?: '____________________' }}</div>
            <div class="cargo">
                @if($expense->voucher_type === 'recibo_honorarios') Beneficiario / Trabajador
                @elseif($expense->voucher_type === 'declaracion_jurada') Declarante
                @else Proveedor / Emisor
                @endif
            </div>
        </div>

        @if($jass['tesorero'])
        <div class="firma">
            <div class="linea"></div>
            <div class="nombre">{{ strtoupper($jass['tesorero']) }}</div>
            <div class="cargo">Tesorero — {{ $jass['nombre'] }}</div>
        </div>
        @endif

        @if($jass['presidente'])
        <div class="firma">
            <div class="linea"></div>
            <div class="nombre">{{ strtoupper($jass['presidente']) }}</div>
            <div class="cargo">Presidente — {{ $jass['nombre'] }}</div>
        </div>
        @endif
    </div>

    <div class="footer">
        Documento generado por el Sistema de Gestión de {{ $jass['nombre'] }} — {{ date('d/m/Y H:i') }}
    </div>

</body>
</html>