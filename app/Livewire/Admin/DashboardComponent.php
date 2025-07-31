<?php

namespace App\Livewire\Admin;

use App\Livewire\Traits\AttendanceDetailTrait;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Carbon\Carbon;

class DashboardComponent extends Component
{
    use AttendanceDetailTrait;

    public function render()
    {
        $today = date('Y-m-d');

        /** @var Collection<Attendance>  */
        $attendances = Attendance::where('date', $today)->get();

        /** @var Collection<User>  */
        $employees = User::with(['todaySchedule.shift'])
        ->where('group', 'user')
        ->paginate(20)
        ->through(function (User $user) use ($attendances) {
            $attendance = $attendances
                ->where(fn (Attendance $attendance) => $attendance->user_id === $user->id)
                ->first();
        
            $today = Carbon::today('Asia/Jakarta');
            $isWeekend = $today->isWeekend();
            $hasSchedule = !is_null($user->todaySchedule?->shift);
        
            $status = 'belum hadir';
        
            $isWeekend = Carbon::today('Asia/Jakarta')->isWeekend();
            $hasScheduleToday = !is_null($user->todaySchedule?->shift);
            
            if ($attendance) {
                $status = match ($attendance->status) {
                    'present' => 'hadir',
                    'late' => 'terlambat',
                    'excused' => 'izin/cuti',
                    'sick' => 'sakit',
                    default => 'belum hadir',
                };
            } elseif ($isWeekend && !$hasScheduleToday) {
                $status = 'hari ini libur (weekend)';
            } elseif ($hasScheduleToday) {
                $status = 'belum hadir';
            } else {
                $status = 'belum ada jadwal';
            }
            // dd($user->todaySchedule);

        
            return $user
                ->setAttribute('attendance', $attendance)
                ->setAttribute('shift', $user->todaySchedule?->shift)
                ->setAttribute('status_hari_ini', $status);
        });
        
    
            
        // Statistik rekap
        $employeesCount = User::where('group', 'user')->count();
        $presentCount = $attendances->whereIn('status', ['present', 'late'])->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $excusedCount = $attendances->where('status', 'excused')->count();
        $sickCount = $attendances->where('status', 'sick')->count();

        $date = Carbon::now();
        $absentCount = $employees->filter(function ($employee) use ($date) {
            $attendance = $employee->attendance;
            $hasSchedule = !is_null($employee->todaySchedule?->shift);
            $isWeekend = $date->isWeekend();
        
            if ($isWeekend && !$hasSchedule) {
                return false; 
            }
        
            return is_null($attendance) || $attendance->status === 'absent';
        })->count();

        return view('livewire.admin.dashboard', [
            'employees' => $employees,
            'employeesCount' => $employeesCount,
            'presentCount' => $presentCount,
            'lateCount' => $lateCount,
            'excusedCount' => $excusedCount,
            'sickCount' => $sickCount,
            'absentCount' => $absentCount,
        ]);
    }
}
