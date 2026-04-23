<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Asistencia - {{ $evento->title }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; font-size: 12px; }
        .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #000; padding-bottom: 12px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; text-transform: uppercase; }
        .header h2 { font-size: 14px; margin: 0 0 4px; color: #333; }
        .header p  { margin: 2px 0; color: #555; font-size: 11px; }
        .stats { display: flex; justify-content: center; gap: 30px; margin: 16px 0; }
        .stat { text-align: center; border: 1px solid #ddd; padding: 8px 20px; border-radius: 8px; }
        .stat .num { font-size: 22px; font-weight: bold; }
        .stat .lbl { font-size: 9px; text-transform: uppercase; color: #666; }
        .verde  { color: #16a34a; }
        .rojo   { color: #dc2626; }
        .amarillo { color: #ca8a04; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th { background: #f1f5f9; padding: 8px; text-align: left; font-size: 10px; text-transform: uppercase; border: 1px solid #ddd; }
        td { padding: 6px 8px; border: 1px solid #ddd; font-size: 11px; }
        .badge { padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: bold; display: inline-block; }
        .badge-presente    { background: #dcfce7; color: #16a34a; }
        .badge-ausente     { background: #fee2e2; color: #dc2626; }
        .badge-justificado { background: #fef9c3; color: #ca8a04; }
        tr:nth-child(even) { background: #f9f9f9; }
        .footer { margin-top: 24px; text-align: center; font-size: 10px; color: #888; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $jass['nombre'] }}</h1>
        <h2>Lista de Asistencia — {{ strtoupper($evento->type) }}</h2>
        <p><strong>{{ $evento->title }}</strong></p>
        <p>Fecha: {{ $evento->date->format('d/m/Y') }}</p>
        @if($evento->description)
        <p>{{ $evento->description }}</p>
        @endif
        <p>Estado: {{ $evento->lista_cerrada ? '🔒 Lista cerrada' : '✏️ Lista abierta' }}</p>
    </div>

    @php
        $presentes    = $evento->attendances->where('status', 'presente');
        $ausentes     = $evento->attendances->where('status', 'ausente');
        $justificados = $evento->attendances->where('status', 'justificado');
        $total        = $evento->attendances->count();
    @endphp

    <div class="stats">
        <div class="stat">
            <div class="num verde">{{ $presentes->count() }}</div>
            <div class="lbl">Presentes</div>
        </div>
        <div class="stat">
            <div class="num rojo">{{ $ausentes->count() }}</div>
            <div class="lbl">Ausentes</div>
        </div>
        <div class="stat">
            <div class="num amarillo">{{ $justificados->count() }}</div>
            <div class="lbl">Justificados</div>
        </div>
        <div class="stat">
            <div class="num" style="color:#333">{{ $total }}</div>
            <div class="lbl">Total</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Apellidos y Nombres</th>
                <th>DNI</th>
                <th>Sector</th>
                <th>N° Medidor</th>
                <th>Asistencia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($evento->attendances->sortBy(fn($a) => $a->associate->last_name ?? '') as $i => $att)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ strtoupper($att->associate->last_name ?? '') }}</strong>, {{ $att->associate->name ?? '' }}</td>
                <td>{{ $att->associate->dni ?? '' }}</td>
                <td>{{ $att->associate->sector->name ?? '—' }}</td>
                <td>{{ $att->associate->meter_number ?? '—' }}</td>
                <td>
                    <span class="badge badge-{{ $att->status }}">
                        {{ strtoupper($att->status) }}
                    </span>
                </td>
                <!-- <td style="width: 80px;">&nbsp;</td> -->
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generado por {{ $jass['nombre'] }} — {{ date('d/m/Y H:i') }}</p>
    </div>

</body>
</html>