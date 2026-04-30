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
                if (is_array($monthData)) {
                    $meses[] = [
                        'etiqueta' => $monthData['name'] ?? $monthData['month'] ?? 'Concepto',
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
        $pdf->setPaper('a4', 'portrait');

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
