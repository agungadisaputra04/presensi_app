<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use Carbon\Carbon;

class LeaveRequestManager extends Component
{
    public function render()
    {
        return view('livewire.admin.leave-request-manager', [
            'leaveRequests' => LeaveRequest::latest()->with('user')->get(),
        ]);
    }

    public function approve($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);
        $leaveRequest->status = 'approved';
        $leaveRequest->save();

        $start = Carbon::parse($leaveRequest->from);
        $end = Carbon::parse($leaveRequest->to);

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            Attendance::updateOrCreate([
                'user_id' => $leaveRequest->user_id,
                'date' => $date->format('Y-m-d'),
            ], [
                'status' => $leaveRequest->type, 
                'note' => $leaveRequest->note,
                'attachment' => $leaveRequest->attachment,
                'work_mode' => 'leave',
            ]);
        }

        session()->flash('message', 'Pengajuan berhasil disetujui dan dimasukkan ke absensi.');
    }
}
