<?php

namespace App\Livewire\Admin;

use App\Models\Associate;
use App\Models\ExtraordinaryPayment;
use App\Models\ExtraordinaryPaymentType;
use App\Models\Payment;
use App\Models\Sector;
use App\Models\Setting;
use App\Services\ReportService;
use App\Services\PdfExportService;
use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
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

    // Filtro cuotas extraordinarias
    public $filtroTipoCuotaId = null;

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
    // PROPIEDADES COMPUTADAS
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

    public function getMorososData(): Collection
    {
        return $this->reportService()->getMorososData();
    }

    public function getBalanceData(): array
    {
        return $this->reportService()->getBalanceData();
    }

    public function getAltasBajasData(): array
    {
        return $this->reportService()->getAltasBajasData();
    }

    public function getMultasData(): Collection
    {
        return $this->reportService()->getMultasData();
    }

    public function getAptosParaCorteData(): Collection
    {
        return $this->reportService()->getAptosParaCorteData();
    }

    // =========================================================================
    // CUOTAS EXTRAORDINARIAS — FILTRO Y CÁLCULO
    // =========================================================================

    public function filtrarCuotaExt(?int $id): void
    {
        $this->filtroTipoCuotaId = $id;
    }

    private function calcularDeudoresExtraordinarias(): Collection
    {
        $query = ExtraordinaryPaymentType::where('active', true);

        if ($this->filtroTipoCuotaId) {
            $query->where('id', $this->filtroTipoCuotaId);
        }

        $tipos = $query->get();

        if ($tipos->isEmpty()) {
            return collect();
        }

        $tipoIds = $tipos->pluck('id');

        $socios = Associate::where('status', 'activo')
            ->with('sector')
            ->get();

        // Pagos ya realizados agrupados por socio
        $pagados = ExtraordinaryPayment::whereIn('extraordinary_payment_type_id', $tipoIds)
            ->get()
            ->groupBy('associate_id')
            ->map(fn($rows) => $rows->pluck('extraordinary_payment_type_id')->toArray());

        $deudores = collect();

        foreach ($socios as $socio) {
            $tiposPagadosPorSocio = $pagados->get($socio->id, []);

            $cuotasPendientes = $tipos->filter(
                fn($t) => !in_array($t->id, $tiposPagadosPorSocio)
            );

            if ($cuotasPendientes->isEmpty()) continue;

            $deudores->push([
                'id'                => $socio->id,
                'name'              => $socio->name,
                'last_name'         => $socio->last_name,
                'sector'            => $socio->sector?->name,
                'cuotas_pendientes' => $cuotasPendientes->pluck('name')->toArray(),
                'total_deuda'       => $cuotasPendientes->sum('amount'),
            ]);
        }

        return $deudores->sortBy('last_name')->values();
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
            'morosos'    => $this->getMorososData(),
            'balance'    => $this->getBalanceData(),
            'altasBajas' => $this->getAltasBajasData(),
            'multas'     => $this->getMultasData(),
            'aptosCorte' => $this->getAptosParaCorteData(),
        ], 'reportes-completos');
    }

    public function exportDeudoresExtraordinariasPDF(): mixed
    {
        $tiposCuotaExt               = ExtraordinaryPaymentType::where('active', true)->get();
        $deudoresExtraordinariasData = $this->calcularDeudoresExtraordinarias();

        $jass = [
            'nombre'    => Setting::get('jass_nombre', 'JASS'),
            'direccion' => Setting::get('jass_direccion', ''),
        ];

        $data = [
            'jass'      => $jass,
            'deudores'  => $deudoresExtraordinariasData,
            'tipos'     => $tiposCuotaExt,
            'filtro'    => $this->filtroTipoCuotaId
                ? $tiposCuotaExt->firstWhere('id', $this->filtroTipoCuotaId)?->name
                : 'Todas',
            'fecha'     => now()->format('d/m/Y H:i'),
            'total'     => $deudoresExtraordinariasData->sum('total_deuda'),
        ];

        return response()->streamDownload(function () use ($data) {
            $pdf = Pdf::loadView('pdf.deudores-extraordinarias', $data);
            $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
            $pdf->setPaper('A4', 'portrait');
            echo $pdf->output();
        }, 'deudores-extraordinarias-' . now()->format('Ymd') . '.pdf');
    }

    // =========================================================================
    // ACCIONES DEL PANEL LATERAL
    // =========================================================================

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

        return Excel::download(new AttendanceExport($eventId), $filename);
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render(): mixed
    {
        $resumenDiario     = $this->reportService()->getDailySummary($this->fecha_filtro);
        $morosos           = $this->reportService()->getMorososModels();
        $associates        = $this->reportService()->getAllAssociates();
        $payments          = $this->reportService()->getPaymentsPaginated(10);
        $sectoresConSocios = $this->reportService()->getSectoresConSocios();

        $tiposCuotaExt               = ExtraordinaryPaymentType::where('active', true)
                                            ->orderBy('name')
                                            ->get();
        $deudoresExtraordinariasData = $this->calcularDeudoresExtraordinarias();

        return view('livewire.admin.report-manager', [
            'resumenDiario'               => $resumenDiario,
            'totalGeneral'                => $resumenDiario->sum('total'),
            'morosos'                     => $morosos,
            'associates'                  => $associates,
            'payments'                    => $payments,
            'sectoresConSocios'           => $sectoresConSocios,
            'morososData'                 => $this->getMorososData(),
            'balanceData'                 => $this->getBalanceData(),
            'altasBajasData'              => $this->getAltasBajasData(),
            'multasData'                  => $this->getMultasData(),
            'aptosCorteData'              => $this->getAptosParaCorteData(),
            'tiposCuotaExt'               => $tiposCuotaExt,
            'deudoresExtraordinariasData' => $deudoresExtraordinariasData,
        ])->layout('layouts.app');
    }
}