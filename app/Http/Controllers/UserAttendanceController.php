<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use App\Models\LeaveRequest;




class UserAttendanceController extends Controller
{
    public ?int $shift_id = null;

    public function applyLeave()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', date('Y-m-d'))
            ->first();
    
        // Ambil semua tanggal absensi user (biar bisa disable di datepicker)
        $disabledDates = Attendance::where('user_id', Auth::id())
            ->pluck('date')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->toArray();
    
        return view('attendances.apply-leave', [
            'attendance' => $attendance,
            'disabledDates' => $disabledDates
        ]);
    }

    public function storeLeaveRequest(Request $request)
    {
        $request->validate([
            'status' => ['required', 'in:excused,sick'],
            'note' => ['required', 'string', 'max:255'],
            'from' => ['required', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'attachment' => ['nullable', 'file', 'max:3072'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'work_mode' => ['nullable', 'in:WFH'],
            'shift_id' => ['nullable', 'integer'],
        ]);
        
        // Parse tanggal dari dan ke
        $fromDate = Carbon::parse($request->from);
        $toDate = $request->to ? Carbon::parse($request->to) : $fromDate;
        
        // Upload attachment jika ada
        $newAttachment = null;
        if ($request->hasFile('attachment')) {
            $newAttachment = $request->file('attachment')->storePublicly(
                'attachments',
                ['disk' => config('jetstream.attachment_disk')]
            );
        }
        
        try {
            // Simpan pengajuan cuti ke tabel leave_requests
            $leaveRequest = LeaveRequest::create([
                'user_id' => Auth::id(),
                'type' => $request->status, 
                'status' => 'pending', // status approval
                'note' => $request->note,
                'from' => $fromDate->format('Y-m-d'),
                'to' => $toDate ? $toDate->format('Y-m-d') : null,
                'attachment' => $newAttachment,
            ]);
            
            
            return redirect(route('home'))->with('flash.banner', __('Pengajuan cuti berhasil, menunggu persetujuan.'));
        } catch (\Throwable $th) {
            return redirect()->back()
                ->with('flash.banner', 'Terjadi kesalahan: ' . $th->getMessage())
                ->with('flash.bannerStyle', 'danger');
        }
    }

    public function approveLeaveRequest($id)
{
    $leaveRequest = LeaveRequest::findOrFail($id);
    $leaveRequest->status = 'approved';
    $leaveRequest->save();

    // Setelah disetujui, tambahkan data presensi ke tabel attendance
    $this->createAttendanceForLeave($leaveRequest);

    return redirect()->back()->with('flash.banner', 'Pengajuan cuti disetujui.');
}

public function rejectLeaveRequest($id)
{
    $leaveRequest = LeaveRequest::findOrFail($id);
    $leaveRequest->status = 'rejected';
    $leaveRequest->save();

    return redirect()->back()->with('flash.banner', 'Pengajuan cuti ditolak.');
}

protected function createAttendanceForLeave(LeaveRequest $leaveRequest)
{
    $period = \Carbon\CarbonPeriod::create($leaveRequest->from, $leaveRequest->to);

    foreach ($period as $date) {
        // Cari jadwal berdasarkan user_id dan tanggal cuti
        $schedule = Schedule::where('user_id', $leaveRequest->user_id)
                            ->whereDate('date', $date->format('Y-m-d'))
                            ->first();

        // Jika jadwal ditemukan, masukkan attendance
        if ($schedule) {
            Attendance::create([
                'user_id' => $leaveRequest->user_id,
                'status' => 'excused',
                'date' => $date->format('Y-m-d'),
                'note' => $leaveRequest->note,
                'attachment' => $leaveRequest->attachment,
                'schedule_id' => $schedule->id, // Menggunakan schedule_id yang valid
            ]);
        } else {
            // Tampilkan alert jika tidak ada jadwal untuk tanggal tersebut
            return redirect()->back()->with('flash.banner', 'Tidak ada jadwal untuk tanggal ' . $date->format('Y-m-d') . '. Pengajuan cuti tidak dapat diproses untuk tanggal tersebut.')
                                     ->with('flash.bannerStyle', 'danger');
        }
    }
}

    
    protected function getScheduleForDate(Carbon $date): ?Schedule
    {
        // Ubah format date ke string YYYY-MM-DD
        $formattedDate = $date->toDateString();
    
        // Log untuk debugging
        \Log::debug("Mencari Jadwal untuk User ID: " . Auth::id());
        \Log::debug("Tanggal: $formattedDate");
    
        // Ambil schedule berdasarkan tanggal dan user_id
        $schedule = Schedule::where('user_id', Auth::id())
            ->when($this->shift_id, function ($query) {
                $query->where('shift_id', $this->shift_id);
            })
            ->whereDate('date', $formattedDate)
            ->first();
    
        if (!$schedule) {
            \Log::debug('Jadwal tidak ditemukan', ['tanggal' => $formattedDate]);
        }
    
        return $schedule;
    }
    
    

    // Di dalam UserAttendanceController

public function history()
{
    // Ambil data pengajuan cuti yang terkait dengan user yang login
    $leaveRequests = LeaveRequest::where('user_id', Auth::id())->get();

    return view('attendances.history', compact('leaveRequests'));
}

public function showAbsenceData(Request $request)
{
    // Ambil data bulan dari filter bulan
    $month = $request->input('month_filter', Carbon::now()->format('Y-m')); // Default bulan saat ini
    $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

    // Ambil data absensi untuk bulan tersebut
    $attendances = Attendance::whereBetween('date', [$startDate, $endDate])->get();

    // Ambil data pengajuan cuti yang disetujui dalam rentang tanggal yang sama
    $leaveRequests = LeaveRequest::whereBetween('from', [$startDate, $endDate])
                                 ->where('status', 'approved')
                                 ->get();

    // Kirim data ke view
    return view('attendance.index', [
        'attendances' => $attendances,
        'leaveRequests' => $leaveRequests,
        'start' => $startDate,
        'end' => $endDate,
        'month' => $month,
    ]);
}

}
