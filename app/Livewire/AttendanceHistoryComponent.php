<?php

namespace App\Livewire;

use App\Livewire\Traits\AttendanceDetailTrait;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;


class AttendanceHistoryComponent extends Component
{
    use AttendanceDetailTrait;

    public ?string $month;
    public $leaveRequests;
    

    public function mount()
    {
        $this->month = date('Y-m');
        $this->leaveRequests = LeaveRequest::where('user_id', Auth::id())->get();
    }

    public function render()
    {
        $user = auth()->user();
        $date = Carbon::parse($this->month);
    
        $start = $date->startOfMonth();
        $end = $date->copy()->endOfMonth();
        $dates = $start->range($end)->toArray();
    
        $attendances = new Collection(Cache::remember(
            "attendance-v2-$user->id-$date->month-$date->year",
            now()->addDay(),
            function () use ($user) {
                return Attendance::filter(
                    month: $this->month,
                    userId: $user->id,
                )->get([
                    'id', 'status', 'date', 'latitude', 'longitude', 'attachment', 'note', 'work_mode'
                ])->map(function (Attendance $v) {
                    $v->setAttribute('coordinates', $v->lat_lng);
                    $v->setAttribute('lat', $v->latitude);
                    $v->setAttribute('lng', $v->longitude);
                    if ($v->attachment) {
                        $v->setAttribute('attachment', $v->attachment_url);
       
                    }
                    $v->setAttribute('work_mode', $v->work_mode);
                    return $v->getAttributes();
                })->toArray();
            }
        ) ?? []);
    
        $attendanceToday = collect($attendances)->firstWhere(fn ($v) => $v['date'] === now()->format('Y-m-d'));
    
        return view('livewire.attendance-history', [
            'attendances' => $attendances,
            'attendanceToday' => $attendanceToday,
            'dates' => $dates,
            'start' => $start,
            'end' => $end,
            'leaveRequests' => $this->leaveRequests,
        ]);
    }
    
}
