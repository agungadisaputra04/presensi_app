<?php

namespace App\Livewire;

use App\ExtendedCarbon;
use App\Models\Attendance;
use App\Models\Barcode;
use App\Models\Schedule;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Ballen\Distical\Calculator as DistanceCalculator;
use Ballen\Distical\Entities\LatLong;
use Illuminate\Support\Carbon;

class ScanComponent extends Component
{
    public ?Attendance $attendance = null;
    public ?int $shift_id = null;
    public $shifts;
    public ?array $currentLiveCoords = null;
    public string $successMsg = '';
    public bool $isAbsence = false;
    public $existingWfhAttendance = null;
    public bool $showWfhButton = false;
    public bool $hasTodaySchedule = false;
    public bool $isOnLeaveOrSick = false;

public $todayAttendance;

    
public bool $isScheduledToWorkToday = false;

    public function mount()
    {
        $this->shifts = Shift::all();
        

       $today = now('Asia/Jakarta');
        $userId = Auth::id();
        $day = $today->format('l'); // 'Monday'
        $month = $today->month;     // 4
        $year = $today->year;       // 2025

logger('Schedule check', [
    'user_id' => $userId,
    'day' => $day,
    'month' => $month,
    'year' => $year,
]);

$userId = Auth::id();
$today = today();

// Cek apakah user ada jadwal kerja hari ini
$this->isScheduledToWorkToday = Schedule::where('user_id', $userId)
    ->whereDate('date', $today)
    ->exists();

// Ambil absensi hari ini (jika ada)
$this->todayAttendance = Attendance::where('user_id', $userId)
    ->whereDate('date', $today)
    ->first();

// Cek apakah user statusnya tidak fit untuk bekerja
$this->isOnLeaveOrSick = in_array(optional($this->todayAttendance)->status, ['sick', 'excused']);

// Cek apakah sudah absen
$this->isAbsence = !is_null(optional($this->todayAttendance)->time_in);

        // Check for existing WFH attendance
        $this->existingWfhAttendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', today())
            ->where('work_mode', 'wfh')
            ->first();


            $this->checkAndApplyAutoPermission();
        // Check for any existing attendance
        // $today = Carbon::createFromDate(2025, 4, 22, 'Asia/Jakarta');
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', $today->format('Y-m-d'))
            ->first();
        if ($attendance) {
            $this->setAttendance($attendance);
        } else {
            // Set default shift
            $closest = ExtendedCarbon::now()
                ->closestFromDateArray($this->shifts->pluck('start_time')->toArray());

            $this->shift_id = $this->shifts
                ->firstWhere('start_time', $closest->format('H:i:s'))->id;
        }

        $this->showWfhButton = $this->existingWfhAttendance 
            ? is_null($this->existingWfhAttendance->time_out)
            : true;

            
    }

    // public function handleWfhAttendance()
    // {
    //     $now = Carbon::now();
    //     $date = $now->format('Y-m-d');
    //     $timeIn = $now->format('H:i:s');
    
    //     // Ambil jadwal hari ini untuk WFH
    //     $schedule = $this->getTodaySchedule();
    
    //     // Jika tidak ada jadwal WFH
    //     if (!$schedule) {
    //         $this->dispatch('alert', type: 'error', message: 'Tidak ada jadwal WFH hari ini.');
    //         return;
    //     }
    
    //     // Ambil shift berdasarkan shift_id dari jadwal
    //     $shift = Shift::find($schedule->shift_id);
        
    //     // Pastikan shift ditemukan
    //     if (!$shift) {
    //         $this->dispatch('alert', type: 'error', message: 'Shift tidak ditemukan.');
    //         return;
    //     }
    
    //     // Tentukan jam mulai shift
    //     $shiftStart = Carbon::today()->setTimeFromTimeString($shift->start_time);
    
    //     // Cek apakah saat ini sudah melewati jam mulai shift
    //     if ($now->lt($shiftStart)) {
    //         $this->dispatch('alert', type: 'error', message: 'Belum bisa absen, shift kamu dimulai pukul ' . $shiftStart->format('H:i'));
    //         return;
    //     }
    
    //     // Tentukan status absensi
    //     $status = $now->gt($shiftStart) ? 'late' : 'present';
    
    //     // Simpan data absensi
    //     $attendance = Attendance::create([
    //         'user_id' => Auth::id(),
    //         'schedule_id' => $schedule->id,
    //         'barcode_id' => null,  // Tidak ada barcode untuk WFH
    //         'date' => $date,
    //         'time_in' => $timeIn,
    //         'time_out' => null,
    //         'shift_id' => $shift->id,
    //         'latitude' => doubleval($this->currentLiveCoords[0]),
    //         'longitude' => doubleval($this->currentLiveCoords[1]),
    //         'status' => $status,
    //         'note' => null,
    //         'attachment' => null,
    //         'work_mode' => 'wfh',  // Menandakan absensi WFH
    //     ]);
    
    //     // Kirim pesan sukses
    //     $this->successMsg = __('Attendance In Successful (WFH)');
    //     $this->setAttendance($attendance);
    
    //     // Clear cache absensi
    //     Attendance::clearUserAttendanceCache(Auth::user(), Carbon::parse($attendance->date));
    // }
    

    public function checkAndApplyAutoPermission()
    {
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
    
        $schedule = $this->getTodaySchedule();
    
        if (!$schedule) return;
    
        $hasAttendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', $today)
            ->exists();
    
        if ($hasAttendance) return;
    
        $shift = Shift::find($schedule->shift_id);
        $shiftStart = Carbon::parse($today . ' ' . $shift->start_time, 'Asia/Jakarta');
    
        $diffInHours = $shiftStart->diffInHours($now, false);
    
        if ($diffInHours >= 4) {
            Attendance::create([
                'user_id' => Auth::id(),
                'schedule_id' => $schedule->id,
                'barcode_id' => null,
                'date' => $today,
                'time_in' => null,
                'time_out' => null,
                'shift_id' => $shift->id,
                'latitude' => null,
                'longitude' => null,
                'status' => 'excused',
                'note' => 'Otomatis diberikan izin karena tidak hadir lebih dari 4 jam.',
                'attachment' => null,
                'work_mode' => null,
            ]);
    
            $this->dispatch('alert', type: 'info', message: 'Kamu telah diberikan Cuti otomatis karena tidak hadir selama lebih dari 4 jam.');
        }
    }
    
    public function handleWfhAttendance()
    {
        $now = Carbon::now();
        $date = $now->format('Y-m-d');
        $timeIn = $now->format('H:i:s');
    
        // Cek jika sudah ada status 'excused' di tanggal yang sama
        $existingAttendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', $date)
            ->where('status', 'excused')
            ->first();
    
        if ($existingAttendance) {
            $this->dispatch('alert', type: 'error', message: 'Kamu sudah diberi izin absen hari ini.');
            return;
        }
    
        $schedule = $this->getTodaySchedule();
    
        if (!$schedule) {
            $this->dispatch('alert', type: 'error', message: 'Tidak ada jadwal WFH hari ini.');
            return;
        }
    
        $shift = Shift::find($schedule->shift_id);
        $shiftStart = Carbon::today()->setTimeFromTimeString($shift->start_time);
    
        if ($now->lt($shiftStart)) {
            $this->dispatch('alert', type: 'error', message: 'Belum bisa absen, shift kamu dimulai pukul ' . $shiftStart->format('H:i'));
            return;
        }
    
        $toleransiMenit = 17;
        $shiftStart = Carbon::today()->setTimeFromTimeString($shift->start_time);
        $waktuToleransi = $shiftStart->copy()->addMinutes($toleransiMenit);

        $status = $now->gt($waktuToleransi) ? 'late' : 'present';

    
        $attendance= Attendance::create([
            'user_id' => Auth::id(),
            'schedule_id' => $schedule->id,
            'barcode_id' => null,
            'date' => $date,
            'time_in' => $timeIn,
            'time_out' => null,
            'shift_id' => $shift->id,
            'latitude' => doubleval($this->currentLiveCoords[0]),
            'longitude' => doubleval($this->currentLiveCoords[1]),
            'status' => $status,
            'note' => null,
            'attachment' => null,
            'work_mode' => 'wfh',
        ]);
    

    
        $this->successMsg = __('Attendance In Successful (WFH)');
        $this->setAttendance($attendance);
        Attendance::clearUserAttendanceCache(Auth::user(), Carbon::parse($attendance->date));
    }
    

    // public function calculateDistance(LatLong $a, LatLong $b)
    // {
    //     $distanceCalculator = new DistanceCalculator($a, $b);
    //     $distanceInMeter = floor($distanceCalculator->get()->asKilometres() * 1000); // convert to meters
    //     return $distanceInMeter;
    // }
    
    public function calculateDistanceRaw($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters
    
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);
    
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
    
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    
        $distance = round($angle * $earthRadius); // in meters
        
        // Log the calculated distance
        logger([
            'lat1' => $lat1,
            'lon1' => $lon1,
            'lat2' => $lat2,
            'lon2' => $lon2,
            'calculated_distance' => $distance
        ]);
        
        return $distance;
    }
    
    public function calculateDistance(LatLong $a, LatLong $b)
    {
        $lat1 = $a->getLatitude();
        $lon1 = $a->getLongitude();
        $lat2 = $b->getLatitude();
        $lon2 = $b->getLongitude();
    
        $distance = $this->calculateDistanceRaw($lat1, $lon1, $lat2, $lon2);
    
        logger([
            'lat1' => $lat1,
            'lon1' => $lon1,
            'lat2' => $lat2,
            'lon2' => $lon2,
            'distance_in_m' => $distance
        ]);
    
        return $distance;
    }
    


    /** @return Attendance */
    public function createAttendance(Barcode $barcode)
    {
        $now = Carbon::now();
        $date = $now->format('Y-m-d');
        $timeIn = $now->format('H:i:s');
        /** @var Shift */
        $shift = Shift::find($this->shift_id);
        $schedule = $this->getTodaySchedule();
        $status = Carbon::now()->setTimeFromTimeString($shift->start_time)->lt($now) ? 'late' : 'present';
        return Attendance::create([
            'user_id' => Auth::user()->id,
            'schedule_id' => $schedule->id,
            'barcode_id' => $barcode->id,
            'date' => $date,
            'time_in' => $timeIn,
            'time_out' => null,
            'shift_id' => $shift->id,
            'latitude' => doubleval($this->currentLiveCoords[0]),
            'longitude' => doubleval($this->currentLiveCoords[1]),
            'status' => $status,
            'note' => null,
            'attachment' => null,
            'Work_mode' => 'wfo',
        ]);
    }



    public function scan(string $barcode)
    {
        $today = now('Asia/Jakarta');
    
        // Cek apakah sudah ada absensi di hari ini
        $attendanceExists = Attendance::where('user_id', Auth::id())
            ->whereDate('date', $today->format('Y-m-d'))
            ->exists();
    
        if ($attendanceExists) {
            $this->dispatch('alert', type: 'error', message: __('You have already checked in or out on this date.'));
            return;
        }
    
        if (is_null($this->currentLiveCoords)) {
            return __('Invalid location');
        }
    
        // Proses absensi barcode, anggap ini WFO
        if (is_null($this->shift_id)) {
            return __('Invalid shift');
        }
    
        /** @var Barcode|null */
        $barcode = Barcode::firstWhere('value', $barcode);
    
        if (!Auth::check() || !$barcode) {
            return __('Invalid barcode');
        }
    
        $lat = $this->currentLiveCoords[0] ?? null;
        $lng = $this->currentLiveCoords[1] ?? null;
    
        if (
            empty($this->currentLiveCoords) ||
            !is_array($this->currentLiveCoords) ||
            count($this->currentLiveCoords) !== 2
        ) {
            return __('Invalid location');
        }
        
        [$lat, $lng] = $this->currentLiveCoords;
        
        if (!is_numeric($lat) || !is_numeric($lng) || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return __('Invalid coordinates');
        }
        
        if (
            !is_numeric($barcode->latitude) || $barcode->latitude < -90 || $barcode->latitude > 90 ||
            !is_numeric($barcode->longitude) || $barcode->longitude < -180 || $barcode->longitude > 180
        ) {
            return __('Invalid barcode coordinates');
        }
    
        $barcodeLocation = new LatLong($barcode->latitude, $barcode->longitude);
        $userLocation = new LatLong($lat, $lng);
    
        if (($distance = $this->calculateDistance($userLocation, $barcodeLocation)) > $barcode->radius) {
            return __('Location out of range') . ": {$distance}m. Max: {$barcode->radius}m";
        }
    
        // Proses absensi WFO karena barcode yang discan
        $this->handleWfoAttendance($barcode);
        
        return true;
    }

    public function handleCheckoutWfh()
{
    $userId = auth()->id();
    $attendance = Attendance::where('user_id', $userId)
        ->whereDate('date', today())
        ->where('work_mode', 'wfh')
        ->whereNotNull('time_in')
        ->whereNull('time_out')
        ->first();

    if ($attendance) {
        $attendance->update([
            'time_out' => now(),
        ]);

        session()->flash('message', 'Check out WFH berhasil!');
    } else {
        session()->flash('error', 'Data absensi tidak ditemukan atau sudah check out.');
    }
}

public function handleCheckoutWfo()
{
    $userId = auth()->id();
    $attendance = Attendance::where('user_id', $userId)
        ->whereDate('date', today())
        ->where('work_mode', 'wfo')
        ->whereNotNull('time_in')
        ->whereNull('time_out')
        ->first();

    if ($attendance) {
        $attendance->update([
            'time_out' => now(),
        ]);

        session()->flash('message', 'Check out WFO berhasil!');
        $this->setAttendance($attendance->fresh());
    } else {
        session()->flash('error', 'Data absensi tidak ditemukan atau sudah check out.');
    }
}

    
// public function handleWfoAttendance($barcode)


// {
//     $now = Carbon::now();
//     $date = $now->format('Y-m-d');
//     $timeIn = $now->format('H:i:s');

//     $schedule = $this->getTodaySchedule();

//     if (!$schedule) {
//         $this->dispatch('alert', type: 'error', message: 'Tidak ada jadwal hari ini.');
//         return;
//     }

//     $shift = Shift::find($schedule->shift_id);
//     $shiftStart = Carbon::today()->setTimeFromTimeString($shift->start_time);

//     if ($now->lt($shiftStart)) {
//         $this->dispatch('alert', type: 'error', message: 'Belum bisa absen, shift kamu dimulai pukul ' . $shiftStart->format('H:i'));
//         return;
//     }

//     $status = $now->gt($shiftStart) ? 'late' : 'present';

//     $attendance = Attendance::create([
//         'user_id' => Auth::id(),
//         'schedule_id' => $schedule->id,
//         'barcode_id' => $barcode->id,
//         'date' => $date,
//         'time_in' => $timeIn,
//         'time_out' => null,
//         'shift_id' => $shift->id,
//         'latitude' => doubleval($this->currentLiveCoords[0]),
//         'longitude' => doubleval($this->currentLiveCoords[1]),
//         'status' => $status,
//         'note' => null,
//         'attachment' => null,
//         'work_mode' => 'wfo',
//     ]);

//     $this->successMsg = __('Attendance In Successful');
//     $this->setAttendance($attendance);
//     Attendance::clearUserAttendanceCache(Auth::user(), Carbon::parse($attendance->date));
// }



public function handleWfoAttendance($barcode)
{
    $now = Carbon::now();
    $date = $now->format('Y-m-d');
    $timeIn = $now->format('H:i:s');

    // Cek jika sudah ada status 'excused' di tanggal yang sama
    $existingAttendance = Attendance::where('user_id', Auth::id())
        ->whereDate('date', $date)
        ->where('status', 'excused')
        ->first();

    if ($existingAttendance) {
        $this->dispatch('alert', type: 'error', message: 'Kamu sudah diberi izin absen hari ini.');
        return;
    }

    // Jika tidak ada status 'excused', lanjutkan pengecekan absen
    $schedule = $this->getTodaySchedule();
    if (!$schedule) {
        $this->dispatch('alert', type: 'error', message: 'Tidak ada jadwal hari ini.');
        return;
    }

    $shift = Shift::find($schedule->shift_id);
    $shiftStart = Carbon::today()->setTimeFromTimeString($shift->start_time);

    if ($now->lt($shiftStart)) {
        $this->dispatch('alert', type: 'error', message: 'Belum bisa absen, shift kamu dimulai pukul ' . $shiftStart->format('H:i'));
        return;
    }

    $toleransiMenit = 17;
    $shiftStart = Carbon::today()->setTimeFromTimeString($shift->start_time);
    $waktuToleransi = $shiftStart->copy()->addMinutes($toleransiMenit);

    $status = $now->gt($waktuToleransi) ? 'late' : 'present';

    // ✅ Simpan ke variabel
    $attendance = Attendance::create([
        'user_id' => Auth::id(),
        'schedule_id' => $schedule->id,
        'barcode_id' => $barcode->id,
        'date' => $date,
        'time_in' => $timeIn,
        'time_out' => null,
        'shift_id' => $shift->id,
        'latitude' => doubleval($this->currentLiveCoords[0]),
        'longitude' => doubleval($this->currentLiveCoords[1]),
        'status' => $status,
        'note' => null,
        'attachment' => null,
        'work_mode' => 'wfo',
    ]);

    $this->successMsg = __('Attendance In Successful');
    $this->setAttendance($attendance);
    Attendance::clearUserAttendanceCache(Auth::user(), Carbon::parse($attendance->date));
}


protected function setAttendance(Attendance $attendance)
    {
        $this->attendance = $attendance;
        $this->shift_id = $attendance->shift_id;
        $this->isAbsence = in_array($attendance->status, ['present', 'late']);
    }

    // protected function getTodaySchedule(): ?Schedule
    // {
    //     $today = Carbon::now('Asia/Jakarta');  // Ambil tanggal hari ini
    //     //$today = Carbon::createFromDate(2025, 4, 22, 'Asia/Jakarta');
    //     $dayOfWeek = $today->format('l');  // Nama hari (misalnya "Monday")
    //     $month = $today->month;  // Bulan saat ini (misalnya 4 untuk April)
    //     $year = $today->year;  // Tahun saat ini
    
    //     return Schedule::where('user_id', Auth::id())
    //     ->where('shift_id', $this->shift_id)
    //     ->whereDate('date', $today)
    //     ->first();
    //     if (!$schedule) {
    //         // Debugging: Jika jadwal tidak ditemukan
    //         dd('Jadwal tidak ditemukan untuk user ini di hari ini.', compact('today', 'dayOfWeek', 'month', 'year'));
    //     }
    
    //     return $schedule;
    // }
    
    protected function getTodaySchedule(): ?Schedule
    {
        $today = Carbon::now('Asia/Jakarta')->toDateString();
    
        return Schedule::where('user_id', Auth::id())
            ->whereDate('date', $today)
            ->first();
    }
    

    public function getAttendance()
    {
        if (is_null($this->attendance)) {
            return null;
        }

        return [
            'time_in' => $this->attendance?->time_in,
            'time_out' => $this->attendance?->time_out,
        ];
    }

    public function render()
    {
        return view('livewire.scan', [
            'hasTodaySchedule' => $this->hasTodaySchedule,
            'isOnLeaveOrSick' => $this->isOnLeaveOrSick,
        ]);
    }
}
