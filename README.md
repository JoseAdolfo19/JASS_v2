# JASS_v2

Sistema administrativo para la gestión de cobros y operaciones de una JASS.

## Qué hace este proyecto

JASS_v2 es una aplicación web construída con Laravel y Livewire para manejar:

- Gestión de asociados y sectores.
- Cobro de cuotas y registro de pagos.
- Historial de pagos y generación de recibos en PDF.
- Control de morosidad y cálculo de multas.
- Reportes administrativos.
- Registro de egresos.
- Configuración de tarifas y datos institucionales.
- Control de asistencia para eventos y faenas.

## Tecnologías principales

- PHP 8.2
- Laravel 12
- Livewire 4
- Tailwind CSS + Vite
- DomPDF para descargas de recibos PDF
- Maatwebsite Excel para exportaciones

## Rutas y flujo principal

- `/` — Página de bienvenida + acceso al login.
- `/login` — Muestra el mismo formulario de inicio de sesión.
- `/home` — Panel administrativo principal (requiere autenticación).
- `/sectores` — Gestión de sectores.
- `/asociados` — Gestión de asociados.
- `/pagos` — Tabla de cobro de cuotas.
- `/historial-pagos` — Historial de pagos.
- `/recibo/{id}` — Genera el recibo PDF de un pago.
- `/admin/reportes` — Módulo de reportes.
- `/admin/egresos` — Gestión de egresos.
- `/admin/configuracion` — Ajustes de tarifas y datos de la JASS.

## Cómo usarlo sin leer el código

1. Copia `.env.example` a `.env`.
2. Ajusta los datos de conexión a tu base de datos MySQL.
3. Ejecuta:
   - `composer install`
   - `php artisan key:generate`
   - `php artisan migrate`
   - `php artisan db:seed` (opcional si quieres datos iniciales)
   - `npm install`
   - `npm run build`
4. Inicia la app con `php artisan serve` y abre el navegador en `http://127.0.0.1:8000`.

## Punto de entrada del usuario

La pantalla inicial contiene un formulario de acceso. Si el usuario se autentica con credenciales válidas, es redirigido al dashboard de administración.

## Nota rápida

Este proyecto está diseñado para ser usado por el staff de la JASS y centraliza la administración de cobros, multas, pagos y asistentes en una sola interfaz.

