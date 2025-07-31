<?php

namespace App\Exports;

use App\Models\Schedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SchedulesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Schedule::select('user_id', 'shift_id', 'date', 'day')->get();
    }

    public function headings(): array
    {
        return ['user_id', 'shift_id', 'date', 'day'];
    }
}


