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

## Cambios recientes importantes

- Se mejoró la generación de recibos PDF para soporte de boletas con tamaño fijo y filas múltiples.
- La plantilla de recibo ahora produce dos boletas por fila: copia **TESORERO** y copia **USUARIO**.
- Se ajustaron márgenes y espaciado para que las boletas queden bien posicionadas en A4 landscape.
- Las boletas ahora caben en una estructura de 10cm de ancho por 8cm de alto, con separación entre ellas y espacio desde el borde superior e izquierdo.
- Se añadió la función `numeroALetras()` en `app/Livewire/Admin/PaymentTable.php` para pasar el monto en palabras al PDF.
- Se corrigió la carga de variables necesarias en la vista `resources/views/pdf/recibo.blade.php`: `monto_en_letras`, `meses_text` y `fecha_recibo`.
 - El contador de socios ahora incluye las instalaciones/conexiones secundarias (no primarias). Los totales de "Activos" y "Suspendidos" suman también las conexiones adicionales por su estado, y el badge "En página" muestra la suma de socios + instalaciones adicionales visibles en la página. Archivos clave: [app/Livewire/Admin/AssociateManager.php](app/Livewire/Admin/AssociateManager.php) y [resources/views/livewire/admin/associate-manager.blade.php](resources/views/livewire/admin/associate-manager.blade.php).

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

## Estructura — carpetas y archivos relevantes

A continuación se listan las rutas y carpetas más importantes para entender y modificar la funcionalidad administrativa:

- [app/Livewire/Admin](app/Livewire/Admin): Componentes Livewire del área administrativa. Ejemplos: `AssociateManager.php`, `PaymentTable.php`.
- [app/Models](app/Models): Modelos Eloquent principales: `Associate.php`, `Connection.php`, `Payment.php`, `Sector.php`, `Setting.php`.
- [database/migrations](database/migrations): Migraciones para tablas como `associates`, `connections`, `payments`, `sectors` y ajustes posteriores (soft deletes, campos adicionales).
- [resources/views/layouts](resources/views/layouts): Layouts principales (p. ej. `app.blade.php`) usados por las vistas y componentes.
- [resources/views/livewire/admin](resources/views/livewire/admin): Vistas Blade usadas por los componentes Livewire administrativos. Ejemplo: `associate-manager.blade.php`.
- [resources/views/pdf](resources/views/pdf): Plantillas para generación de PDFs (recibos, reportes). Ejemplo: `recibo.blade.php`.

Nota: Recientemente se actualizó la lógica del padrón para que las instalaciones/conexiones secundarias se incluyan en los totales y en el badge "En página". Los cambios principales están en [app/Livewire/Admin/AssociateManager.php](app/Livewire/Admin/AssociateManager.php) y en la vista [resources/views/livewire/admin/associate-manager.blade.php](resources/views/livewire/admin/associate-manager.blade.php).

