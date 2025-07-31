<?php

namespace App\Http\Controllers;

use App\Exports\SchedulesExport;
use App\Imports\SchedulesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ScheduleController extends Controller
{
    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,csv']);

        Excel::import(new SchedulesImport, $request->file('file'));

        return back()->with('message', 'Jadwal berhasil diimport!');
    }

    public function export()
    {
        return Excel::download(new SchedulesExport, 'jadwal.xlsx');
    }
}
