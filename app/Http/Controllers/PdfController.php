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
        
        // Procesar meses pagados
        $meses = [];
        if ($payment->months_paid && is_array($payment->months_paid)) {
            foreach ($payment->months_paid as $monthData) {
                if (is_string($monthData)) {
                    // Si es string como "2025-08", convertir a etiqueta legible
                    if (preg_match('/^(\d{4})-(\d{2})$/', $monthData, $matches)) {
                        $year = $matches[1];
                        $month = intval($matches[2]);
                        $monthNames = [
                            1 => 'ENE', 2 => 'FEB', 3 => 'MAR', 4 => 'ABR',
                            5 => 'MAY', 6 => 'JUN', 7 => 'JUL', 8 => 'AGO',
                            9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DIC'
                        ];
                        $etiqueta = ($monthNames[$month] ?? 'MES') . ' ' . $year;
                        $meses[] = [
                            'etiqueta' => $etiqueta,
                            'monto' => 0,
                        ];
                    } else {
                        // Si no es formato YYYY-MM, usar como está
                        $meses[] = [
                            'etiqueta' => $monthData,
                            'monto' => 0,
                        ];
                    }
                } elseif (is_array($monthData)) {
                    // Si es array, usar los datos
                    $meses[] = [
                        'etiqueta' => $monthData['name'] ?? $monthData['month'] ?? $monthData['etiqueta'] ?? 'Concepto',
                        'monto' => $monthData['amount'] ?? 0,
                    ];
                }
            }
        }
        
        $subtotal = array_sum(array_column($meses, 'monto'));
        $mora = $payment->late_fee_applied ?? 0;
        $fine = $payment->fine_amount ?? 0;
        $total = $payment->amount;

        $pdf = Pdf::loadView('pdf.recibo', compact('payment', 'asociado', 'jass', 'fecha_emision', 'meses', 'subtotal', 'mora', 'fine', 'total'));
        $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', false);
        $pdf->setPaper('a5', 'portrait');

        return $pdf->stream('recibo-'.$id.'.pdf');
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
