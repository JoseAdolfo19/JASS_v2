<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function recibo(int $id)
    {
        $payment = Payment::with('associate')->findOrFail($id);
        $asociado = $payment->associate;
        
        $jass = [
            'nombre'     => Setting::get('jass_nombre', 'JASS'),
            'direccion'  => Setting::get('jass_direccion', ''),
            'presidente' => Setting::get('jass_presidente', ''),
            'tesorero'   => Setting::get('jass_tesorero', ''),
        ];
        $fecha_emision = $payment->created_at->format('d/m/Y H:i');

        $monthNames = [
            1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL',
            5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO',
            9 => 'SETIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'
        ];

        // Procesar meses pagados y generar texto legible
        $meses = [];
        $meses_text = [];
        if ($payment->months_paid && is_array($payment->months_paid)) {
            foreach ($payment->months_paid as $monthData) {
                if (is_string($monthData)) {
                    if (preg_match('/^(\d{4})-(\d{2})$/', $monthData, $matches)) {
                        $year = $matches[1];
                        $month = intval($matches[2]);
                        $label = ($monthNames[$month] ?? 'MES') . ' ' . $year;
                        $meses[] = ['etiqueta' => $label, 'monto' => 0];
                        $meses_text[] = $label;
                    } else {
                        $meses[] = ['etiqueta' => $monthData, 'monto' => 0];
                        $meses_text[] = strtoupper($monthData);
                    }
                } elseif (is_array($monthData)) {
                    $label = $monthData['name'] ?? $monthData['month'] ?? $monthData['etiqueta'] ?? 'Concepto';
                    $meses[] = ['etiqueta' => $label, 'monto' => $monthData['amount'] ?? 0];
                    $meses_text[] = strtoupper($label);
                }
            }
        }

        $meses_text = implode(', ', $meses_text);
        $subtotal = array_sum(array_column($meses, 'monto'));
        $mora = $payment->late_fee_applied ?? 0;
        $fine = $payment->fine_amount ?? 0;
        $total = $payment->amount;
        $fecha_recibo = $payment->created_at->format('d') . ' de ' . ($monthNames[$payment->created_at->month] ?? '') . ' del ' . $payment->created_at->year;
        $monto_en_letras = $this->numeroALetras($payment->amount);

        $pdf = Pdf::loadView('pdf.recibo', compact('payment', 'asociado', 'jass', 'fecha_emision', 'meses', 'subtotal', 'mora', 'fine', 'total', 'meses_text', 'fecha_recibo', 'monto_en_letras'));
        $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', false);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('recibo-'.$id.'.pdf');
    }

    private function numeroALetras(float $numero): string
    {
        $entero = floor($numero);
        $decimales = round(($numero - $entero) * 100);
        
        try {
            $formatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
            $texto = mb_strtoupper($formatter->format($entero));
        } catch (\Throwable $e) {
            $texto = strtoupper(number_format($entero, 0, ',', '.'));
        }

        return trim($texto) . ' CON ' . str_pad($decimales, 2, '0', STR_PAD_LEFT) . '/100 SOLES';
    }

    public function egreso(int $id)
    {
        $expense = Expense::findOrFail($id);
        $jass = [
            'nombre'     => Setting::get('jass_nombre', 'JASS'),
            'direccion'  => Setting::get('jass_direccion', ''),
            'presidente' => Setting::get('jass_presidente', ''),
            'tesorero'   => Setting::get('jass_tesorero', ''),
        ];

        return response()->streamDownload(function () use ($expense, $jass) {
            $pdf = Pdf::loadView('pdf.comprobante-egreso', compact('expense', 'jass'));
            $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
            $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', false);
            $pdf->setPaper('a4', 'portrait');
            echo $pdf->output();
        }, 'egreso-'.$id.'.pdf');
    }
}
