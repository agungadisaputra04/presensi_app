<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use Carbon\CarbonPeriod;

class LeaveApprovalController extends Controller
{
    public function approve($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->status = 'approved';
        $leaveRequest->save();

        $this->createAttendanceForLeave($leaveRequest);

        return redirect()->back()->with('flash.banner', 'Pengajuan cuti disetujui.');
    }

    public function reject($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->status = 'rejected';
        $leaveRequest->save();

        return redirect()->back()->with('flash.banner', 'Pengajuan cuti ditolak.');
    }

    protected function createAttendanceForLeave(LeaveRequest $leaveRequest)
    {
        try {
            // Periode cuti dari tanggal 'from' hingga 'to'
            $period = CarbonPeriod::create($leaveRequest->from, $leaveRequest->to);
        
            foreach ($period as $date) {
                // Cari jadwal kerja yang sesuai berdasarkan tanggal dan user yang mengajukan cuti
                $schedule = \App\Models\Schedule::where('user_id', $leaveRequest->user_id)
                                                ->where('date', $date->format('Y-m-d'))
                                                ->first();
        
                // Tentukan schedule_id sesuai dengan jadwal yang ditemukan
                $schedule_id = $schedule ? $schedule->id : null;
    
                // Membuat absensi untuk cuti
             // Cek tipe izin berdasarkan kondisi yang diinginkan
                $type = ($leaveRequest->type == 'sick') ? 'sick' : 'excused';

                // Menambahkan entri di tabel Attendance
                Attendance::create([
                    'user_id' => $leaveRequest->user_id,
                    'status' => $type, // status bisa 'excused' atau 'sick'
                    'date' => $date->format('Y-m-d'),
                    'note' => $leaveRequest->note,
                    'attachment' => $leaveRequest->attachment,
                    'schedule_id' => $schedule_id,
                ]);
            }
    
        } catch (\Exception $e) {
            // Menangani error jika ada masalah dalam proses pembuatan absensi
            // Misalnya bisa log error atau flash message ke pengguna
            return redirect()->back()->with('flash.banner', 'Terjadi kesalahan saat memproses pengajuan cuti.')->withErrors($e->getMessage());
        }
    }
    
}
