<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Balance de Caja - {{ date('d/m/Y') }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #1a365d; margin: 0; }
        .header p { color: #666; margin: 5px 0; }
        .balance-container { display: flex; justify-content: space-around; margin: 40px 0; }
        .balance-box { border: 2px solid #ddd; padding: 20px; border-radius: 10px; text-align: center; min-width: 200px; }
        .ingresos { border-color: #10b981; background-color: #f0fdf4; }
        .egresos { border-color: #ef4444; background-color: #fef2f2; }
        .saldo { border-color: #3b82f6; background-color: #eff6ff; }
        .amount { font-size: 24px; font-weight: bold; margin: 10px 0; }
        .ingresos .amount { color: #10b981; }
        .egresos .amount { color: #ef4444; }
        .saldo .amount { color: #3b82f6; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>BALANCE DE CAJA</h1>
        <p>Estado financiero simple y transparente</p>
        <p>Fecha de generación: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="balance-container">
        <div class="balance-box ingresos">
            <h3>TOTAL INGRESOS</h3>
            <div class="amount">S/ {{ number_format($data['ingresos'], 2) }}</div>
            <p>Recaudación Total</p>
        </div>

        <div class="balance-box egresos">
            <h3>TOTAL EGRESOS</h3>
            <div class="amount">S/ {{ number_format($data['egresos'], 2) }}</div>
            <p>Gastos Registrados</p>
        </div>

        <div class="balance-box saldo">
            <h3>SALDO DISPONIBLE</h3>
            <div class="amount">S/ {{ number_format($data['saldo'], 2) }}</div>
            <p>Ingresos - Egresos</p>
        </div>
    </div>

    <div class="footer">
        <p>Reporte generado por el Sistema de Gestión JASS - {{ date('Y') }}</p>
        <p>Rendición de cuentas para asambleas generales</p>
    </div>
</body>
</html>