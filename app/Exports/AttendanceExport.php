<?php

namespace App\Exports;

use App\Models\EventAttendance;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, WithHeadings
{
    public function __construct(private int $eventId)
    {
    }

    public function collection(): Collection
    {
        return EventAttendance::with('associate')
            ->where('event_id', $this->eventId)
            ->get()
            ->map(function (EventAttendance $attendance) {
                return [
                    'Evento'       => $attendance->event_id,
                    'Socio'        => trim(optional($attendance->associate)->name . ' ' . optional($attendance->associate)->last_name),
                    'Sector'       => optional(optional($attendance->associate)->sector)->name,
                    'Estado'       => $attendance->status,
                    'Multa pagada' => $attendance->fine_paid ? 'Sí' : 'No',
                    'Creado'       => $attendance->created_at?->format('Y-m-d H:i:s'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Evento',
            'Socio',
            'Sector',
            'Estado',
            'Multa pagada',
            'Creado',
        ];
    }
}
