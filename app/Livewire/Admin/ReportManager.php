<?php

namespace App\Livewire\Admin;

use App\Models\Associate;
use App\Models\Payment;
use App\Models\Sector;
use App\Services\ReportService;
use App\Services\PdfExportService;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;

class ReportManager extends Component
{
    // =========================================================================
    // PROPIEDADES
    // =========================================================================

    public string $tab          = 'diario';
    public string $fecha_filtro = '';

    public bool $showReportDetails = false;
    public bool $showAlertConfig   = false;

    // Formulario de cobro
    public $associate_id;
    public string $type    = 'cuota';
    public $amount;
    public $concept;
    public $detail;
    public $multa_tipo;
    public $otros_concepto;

    // =========================================================================
    // CICLO DE VIDA
    // =========================================================================

    public function mount(): void
    {
        $this->fecha_filtro = date('Y-m-d');
    }

    protected function reportService(): ReportService
    {
        return new ReportService();
    }

    protected function pdfService(): PdfExportService
    {
        return new PdfExportService();
    }

    // =========================================================================
    // PROPIEDADES COMPUTADAS (deuda del socio seleccionado)
    // =========================================================================

    public function getMesesDeudaProperty(): int
    {
        if (!$this->associate_id) return 0;

        $associate = Associate::find($this->associate_id);
        if (!$associate) return 0;

        $fechaInicio = $associate->entry_date
            ? Carbon::parse($associate->entry_date)->startOfMonth()
            : Carbon::parse($associate->created_at)->startOfMonth();

        return $fechaInicio->diffInMonths(Carbon::now()->startOfMonth());
    }

    public function getTextoDeudaProperty(): string
    {
        $meses = $this->meses_deuda;
        if ($meses <= 0) return 'Sin deuda pendiente';

        $plural = $meses > 1 ? 'es' : '';
        return "Deuda de {$meses} mes{$plural} pendiente{$plural}";
    }

    public function getMoraTotalProperty(): float
    {
        $meses = $this->meses_deuda;
        if ($meses < 3) return 0;

        return floor($meses / 3) * 60;
    }

    // =========================================================================
    // ACCIONES DEL FORMULARIO
    // =========================================================================

    public function savePayment(): void
    {
        $this->validate([
            'associate_id' => 'required|exists:associates,id',
            'type'         => 'required|in:cuota,falta,extra',
            'amount'       => 'required|numeric|min:0.01',
        ]);

        $ultimo   = Payment::max('invoice_number');
        $nuevoNro = $ultimo ? intval($ultimo) + 1 : 1;

        Payment::create([
            'invoice_number' => str_pad($nuevoNro, 6, '0', STR_PAD_LEFT),
            'associate_id'   => $this->associate_id,
            'amount'         => $this->amount,
            'type'           => $this->type,
            'concept'        => $this->concept ?: 'Pago registrado',
            'detail'         => $this->detail,
        ]);

        $this->reset(['associate_id', 'type', 'amount', 'concept', 'detail', 'multa_tipo', 'otros_concepto']);

        session()->flash('message', 'Pago registrado correctamente.');
    }

    // =========================================================================
    // HELPERS PRIVADOS
    // =========================================================================

    /**
     * Calcula los meses de deuda de un asociado desde su fecha de ingreso.
     */
    private function calcularMesesDeuda(Associate $associate): int
    {
        $fechaInicio = $associate->entry_date
            ? Carbon::parse($associate->entry_date)->startOfMonth()
            : Carbon::parse($associate->created_at)->startOfMonth();

        return $fechaInicio->diffInMonths(Carbon::now()->startOfMonth());
    }

    // =========================================================================
    // DATOS PARA REPORTES
    // =========================================================================

    /**
     * 1. Padrón General de Morosos
     * Socios sin pagos en los últimos 2 meses.
     */
    public function getMorososData(): Collection
    {
        return $this->reportService()->getMorososData();
    }

    /**
     * 2. Balance de Caja
     * Totales de ingresos, egresos y saldo disponible.
     */
    public function getBalanceData(): array
    {
        return $this->reportService()->getBalanceData();
    }

    /**
     * 3. Reporte de Altas y Bajas
     * Variaciones en el padrón de socios del año actual.
     */
    public function getAltasBajasData(): array
    {
        return $this->reportService()->getAltasBajasData();
    }

    /**
     * 4. Resumen de Deuda por Multas
     * Socios con pagos de tipo 'falta' agrupados.
     */
    public function getMultasData(): Collection
    {
        return $this->reportService()->getMultasData();
    }

    /**
     * 5. Lista de Aptos para Corte
     * Socios sin pagos en los últimos 6 meses.
     */
    public function getAptosParaCorteData(): Collection
    {
        return $this->reportService()->getAptosParaCorteData();
    }

    // =========================================================================
    // EXPORTACIONES PDF
    // =========================================================================

    public function exportMorososPDF(): mixed
    {
        return $this->pdfService()->buildPdf('pdf.reportes.morosos', $this->getMorososData(), 'padron-morosos');
    }

    public function exportBalancePDF(): mixed
    {
        return $this->pdfService()->buildPdf('pdf.reportes.balance', $this->getBalanceData(), 'balance-caja');
    }

    public function exportAltasBajasPDF(): mixed
    {
        return $this->pdfService()->buildPdf('pdf.reportes.altas-bajas', $this->getAltasBajasData(), 'altas-bajas');
    }

    public function exportMultasPDF(): mixed
    {
        return $this->pdfService()->buildPdf('pdf.reportes.multas', $this->getMultasData(), 'deuda-multas');
    }

    public function exportAptosCortePDF(): mixed
    {
        return $this->pdfService()->buildPdf('pdf.reportes.aptos-corte', $this->getAptosParaCorteData(), 'aptos-corte');
    }

    public function exportAllReportsPDF(): mixed
    {
        return $this->pdfService()->buildPdf('pdf.reportes.todos', [
            'morosos'   => $this->getMorososData(),
            'balance'   => $this->getBalanceData(),
            'altasBajas'=> $this->getAltasBajasData(),
            'multas'    => $this->getMultasData(),
            'aptosCorte'=> $this->getAptosParaCorteData(),
        ], 'reportes-completos');
    }

    public function viewDetails(): void
    {
        $this->showReportDetails = true;
        $this->showAlertConfig   = false;
    }

    public function configureAlerts(): void
    {
        $this->showAlertConfig   = true;
        $this->showReportDetails = false;
    }

    public function hideQuickPanel(): void
    {
        $this->showReportDetails = false;
        $this->showAlertConfig   = false;
    }

    public function exportAttendanceExcel($eventId)
    {
        $event = Event::find($eventId);
        
        if (!$event) {
            session()->flash('error', 'El evento no existe.');
            return;
        }

        $filename = 'Asistencia_' . str_replace(' ', '_', $event->name) . '.xlsx';

        // Esto dispara la descarga del Excel con múltiples hojas
        return Excel::download(new AttendanceExport($eventId), $filename);
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render(): mixed
    {
        // Resumen de caja del día filtrado
        $resumenDiario = $this->reportService()->getDailySummary($this->fecha_filtro);

        // Lista de morosos sin mapear (para tabla principal)
        $morosos = $this->reportService()->getMorososModels();

        // Padrón completo ordenado
        $associates = $this->reportService()->getAllAssociates();

        // Historial de pagos paginado
        $payments = $this->reportService()->getPaymentsPaginated(10);

        // Sectores con sus socios
        $sectoresConSocios = $this->reportService()->getSectoresConSocios();

        return view('livewire.admin.report-manager', [
            // Datos generales
            'resumenDiario'     => $resumenDiario,
            'totalGeneral'      => $resumenDiario->sum('total'),
            'morosos'           => $morosos,
            'associates'        => $associates,
            'payments'          => $payments,
            'sectoresConSocios' => $sectoresConSocios,
            // Datos para las tarjetas de reporte
            'morososData'       => $this->getMorososData(),
            'balanceData'       => $this->getBalanceData(),
            'altasBajasData'    => $this->getAltasBajasData(),
            'multasData'        => $this->getMultasData(),
            'aptosCorteData'    => $this->getAptosParaCorteData(),
        ])->layout('layouts.app');
    }
}
