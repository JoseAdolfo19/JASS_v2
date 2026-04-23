<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class PdfExportService
{
    /**
     * Genera y descarga un PDF usando streamDownload (compatible con Livewire).
     * pdf->download() no funciona en Livewire porque choca con la respuesta JSON.
     */
    public function buildPdf(string $view, mixed $data, string $filename): mixed
    {
        $data = $this->sanitizeForPDF($data);

        return response()->streamDownload(function () use ($view, $data) {
            $pdf = Pdf::loadView($view, compact('data'));
            $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
            $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', false);
            $pdf->setPaper('a4', 'portrait');
            echo $pdf->output();
        }, $filename . '-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Sanitiza datos para exportación PDF (escapa HTML, múltiples encodings).
     * Se aplica en buildPdf() antes de renderizar la vista PDF.
     */
    private function sanitizeForPDF(mixed $data): mixed
    {
        if (is_string($data)) {
            foreach (['ISO-8859-1', 'Windows-1252', 'CP1252'] as $encoding) {
                $converted = @iconv($encoding, 'UTF-8//TRANSLIT//IGNORE', $data);
                if ($converted !== false && $converted !== $data) {
                    $data = $converted;
                    break;
                }
            }

            $data = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $data) ?? $data;

            if (!mb_check_encoding($data, 'UTF-8')) {
                $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            }

            $data = html_entity_decode($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
        }

        if (is_array($data)) {
            return array_map([$this, 'sanitizeForPDF'], $data);
        }

        if ($data instanceof Collection) {
            return $data->map(fn($item) => $this->sanitizeForPDF($item));
        }

        if (is_object($data)) {
            foreach (get_object_vars($data) as $key => $value) {
                $data->$key = $this->sanitizeForPDF($value);
            }
            return $data;
        }

        return $data;
    }
}
