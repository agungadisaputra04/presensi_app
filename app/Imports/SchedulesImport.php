<?php

namespace App\Imports;

use App\Models\Schedule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SchedulesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        Log::info('✅ Proses import dijalankan'); // PASTIKAN INI KELUAR DULU

        foreach ($rows as $row) {
            Log::info('🧾 Data baris:', $row->toArray());

            if (
                isset($row['user_id']) &&
                isset($row['shift_id']) &&
                isset($row['date']) &&
                isset($row['day'])
            ) {
                Schedule::updateOrCreate(
                    [
                        'user_id' => $row['user_id'],
                        'date' => $row['date'],
                    ],
                    [
                        'shift_id' => $row['shift_id'],
                        'day' => $row['day'],
                       'month' => date('n', strtotime($row['date'])), 
                        'year' => date('Y', strtotime($row['date'])),
                    ]
                );
                Log::info('✅ Disimpan:', $row->toArray());
            } else {
                Log::warning('⚠️ Data tidak lengkap:', $row->toArray());
            }
        }
    }
}
