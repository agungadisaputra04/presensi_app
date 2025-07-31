<?php

namespace App\Livewire\Admin;

use App\Livewire\Traits\AttendanceDetailTrait;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceComponent extends Component
{
    use AttendanceDetailTrait;
    use WithPagination, InteractsWithBanner;

    # filter
    public ?string $month;
    public ?string $week = null;
    public ?string $date = null;
    public ?string $division = null;
    public ?string $jobTitle = null;
    public ?string $search = null;
    public ?string $workMode = null;
    


    public function mount()
    {
        $this->date = date('Y-m-d');
    }

    public function updating($key): void
    {
        if ($key === 'search' || $key === 'division' || $key === 'jobTitle') {
            $this->resetPage();
        }
        if ($key === 'month') {
            $this->resetPage();
            $this->week = null;
            $this->date = null;
        }
        if ($key === 'week') {
            $this->resetPage();
            $this->month = null;
            $this->date = null;
        }
        if ($key === 'date') {
            $this->resetPage();
            $this->month = null;
            $this->week = null;
        }
        if ($key === 'workMode') {
            $this->resetPage();
        }
    }

    public function render()
    {
        $today = Carbon::parse($this->date ?? now()->toDateString())->toDateString();
    
        // Tentukan rentang tanggal
        $dates = [];
        if ($this->date) {
            $dates = [Carbon::parse($this->date)];
        } elseif ($this->week) {
            $start = Carbon::parse($this->week)->startOfWeek();
            $end = Carbon::parse($this->week)->endOfWeek();
            $dates = $start->range($end)->toArray();
        } elseif ($this->month) {
            $start = Carbon::parse($this->month)->startOfMonth();
            $end = Carbon::parse($this->month)->endOfMonth();
            $dates = $start->range($end)->toArray();
        }
    
        // Ambil user
        $employees = User::where('group', 'user')
            ->when($this->search, function (Builder $q) {
                return $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('nip', 'like', '%' . $this->search . '%');
            })
            ->when($this->division, fn (Builder $q) => $q->where('division_id', $this->division))
            ->when($this->jobTitle, fn (Builder $q) => $q->where('job_title_id', $this->jobTitle))
            ->paginate(20)
            ->through(function (User $user) {
                if ($this->date) {
                    $attendances = new Collection(Cache::remember(
                        "attendance-{$user->id}-{$this->date}",
                        now()->addDay(),
                        function () use ($user) {
                            return Attendance::filter(
                                userId: $user->id,
                                date: $this->date
                            )->when($this->workMode, fn ($q) => $q->where('work_mode', $this->workMode))
                             ->get()
                             ->map(function (Attendance $v) {
                                $v->setAttribute('coordinates', $v->lat_lng);
                                $v->setAttribute('lat', $v->latitude);
                                $v->setAttribute('lng', $v->longitude);
                                if ($v->attachment) {
                                    $v->setAttribute('attachment', $v->attachment_url);
                                }
                                if ($v->shift) {
                                    $v->setAttribute('shift', $v->shift->name);
                                }
                                if ($v->work_mode) {
                                    $v->setAttribute('work_mode', $v->work_mode);
                                }
                                 return $v;
                             })->toArray();
                        }
                    ) ?? []);
                } elseif ($this->week) {
                    $attendances = new Collection(Cache::remember(
                        "attendance-{$user->id}-{$this->week}",
                        now()->addDay(),
                        function () use ($user) {
                            return Attendance::filter(
                                userId: $user->id,
                                week: $this->week
                            )->with('shift')
                            ->get([
                                'id', 'status', 'date', 'work_mode', 'latitude', 'longitude',
                                'attachment', 'note', 'time_in', 'time_out', 'shift_id'
                            ])
                            ->map(function (Attendance $v) {
                                $v->setAttribute('coordinates', $v->lat_lng);
                                $v->setAttribute('lat', $v->latitude);
                                $v->setAttribute('lng', $v->longitude);
                                if ($v->attachment) {
                                    $v->setAttribute('attachment', $v->attachment_url);
                                }
                                if ($v->shift) {
                                    $v->setAttribute('shift', $v->shift->name);
                                }
                                if ($v->work_mode) {
                                    $v->setAttribute('work_mode', $v->work_mode);
                                }
                                 return $v;
                             })->toArray();
                        }
                    ) ?? []);
                } elseif ($this->month) {
                    $my = Carbon::parse($this->month);
                    $attendances = new Collection(Cache::remember(
                        "attendance-{$user->id}-{$my->month}-{$my->year}",
                        now()->addDay(),
                        function () use ($user) {
                            return Attendance::filter(
                                month: $this->month,
                                userId: $user->id
                            )->when($this->workMode, fn ($q) => $q->where('work_mode', $this->workMode))
                            ->with('shift')
                            ->get([
                                'id', 'status', 'date', 'work_mode', 'latitude', 'longitude',
                                'attachment', 'note', 'time_in', 'time_out', 'shift_id'
                            ])
                             ->map(function (Attendance $v) {
                                $v->setAttribute('coordinates', $v->lat_lng);
                                $v->setAttribute('lat', $v->latitude);
                                $v->setAttribute('lng', $v->longitude);
                                if ($v->attachment) {
                                    $v->setAttribute('attachment', $v->attachment_url);
                                }
                                if ($v->shift) {
                                    $v->setAttribute('shift', $v->shift->name);
                                }
                                if ($v->work_mode) {
                                    $v->setAttribute('work_mode', $v->work_mode);
                                }
                                 return $v;
                             })->toArray();
                        }
                    ) ?? []);
                } else {
                    $attendances = Attendance::where('user_id', $user->id)
                        ->get(['id', 'status', 'date', 'work_mode', 'latitude', 'longitude', 'attachment', 'note']);
                }
    
                $user->attendances = $attendances;
                return $user;
            });
    
        // Hitung statistik
        $presentCount = 0;
        $lateCount = 0;
        $sickCount = 0;
        $excusedCount = 0;
        $absentCount = 0;
    
        foreach ($employees as $user) {
            $attendances = collect($user->attendances);
    
            if ($attendances->isEmpty()) {
                $absentCount++;
                continue;
            }
    
            foreach ($attendances as $attendance) {
                $status = is_array($attendance) ? $attendance['status'] : $attendance->status;
    
                switch ($status) {
                    case 'present':
                        $presentCount++;
                        break;
                    case 'late':
                        $lateCount++;
                        break;
                    case 'sick':
                        $sickCount++;
                        break;
                    case 'excused':
                        $excusedCount++;
                        break;
                    default:
                        $absentCount++;
                        break;
                }
            }
        }
    
        return view('livewire.admin.attendance', [
            'employees' => $employees,
            'dates' => $dates,
            'presentCount' => $presentCount,
            'lateCount' => $lateCount,
            'sickCount' => $sickCount,
            'excusedCount' => $excusedCount,
            'absentCount' => $absentCount,
        ]);
    }
    
    
}
