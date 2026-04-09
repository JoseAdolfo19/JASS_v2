<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Reporte de Altas y Bajas - {{ date('d/m/Y') }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #1a365d; margin: 0; }
        .header p { color: #666; margin: 5px 0; }
        .stats-container { display: flex; justify-content: space-around; margin: 40px 0; }
        .stat-box { border: 2px solid #ddd; padding: 20px; border-radius: 10px; text-align: center; min-width: 150px; }
        .altas { border-color: #10b981; background-color: #f0fdf4; }
        .bajas { border-color: #ef4444; background-color: #fef2f2; }
        .crecimiento { border-color: #3b82f6; background-color: #eff6ff; }
        .number { font-size: 36px; font-weight: bold; margin: 10px 0; }
        .altas .number { color: #10b981; }
        .bajas .number { color: #ef4444; }
        .crecimiento .number { color: #3b82f6; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE ALTAS Y BAJAS</h1>
        <p>Registro de variaciones en el padrón de socios - {{ date('Y') }}</p>
        <p>Fecha de generación: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="stats-container">
        <div class="stat-box altas">
            <h3>NUEVOS INSCRITOS</h3>
            <div class="number">{{ $data['altas'] }}</div>
            <p>Altas en {{ date('Y') }}</p>
        </div>

        <div class="stat-box bajas">
            <h3>SOCIOS RETIRADOS</h3>
            <div class="number">{{ $data['bajas'] }}</div>
            <p>Bajas en {{ date('Y') }}</p>
        </div>

        <div class="stat-box crecimiento">
            <h3>CRECIMIENTO NETO</h3>
            <div class="number">{{ $data['crecimiento_neto'] }}</div>
            <p>Altas - Bajas</p>
        </div>
    </div>

    <div class="footer">
        <p>Reporte generado por el Sistema de Gestión JASS - {{ date('Y') }}</p>
        <p>Control del crecimiento de la red de agua</p>
    </div>
</body>
</html>