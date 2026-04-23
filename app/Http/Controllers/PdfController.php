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

        return response()->streamDownload(function () use ($payment) {
            $pdf = Pdf::loadView('pdf.recibo', compact('payment'));
            $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
            $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', false);
            $pdf->setPaper('a4', 'portrait');
            echo $pdf->output();
        }, 'recibo-'.$id.'.pdf');
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
