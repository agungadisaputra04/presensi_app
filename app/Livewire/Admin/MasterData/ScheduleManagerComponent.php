<?php
namespace App\Livewire\Admin\MasterData;

use App\Models\Schedule;
use App\Models\Shift;
use App\Models\User;
use Livewire\Component;
use Carbon\Carbon;

class ScheduleManagerComponent extends Component
{
    public $schedules;
    public $users;
    public $shifts;
    public $selectedUser;
    public $selectedShift;
    public $selectedMonth;
    public $selectedYear;
    public $months; 
    public $year; 
    public $detailSchedules = [];
    public function mount()
    {
        $this->users = User::all();
        $this->shifts = Shift::all();
        $this->users = User::whereNotIn('name', ['admin', 'Super Admin'])->get();
        
        // Mengambil bulan dan tahun dari session jika ada
        $this->selectedMonth = session()->get('selectedMonth', Carbon::now()->month);
        $this->selectedYear = session()->get('selectedYear', Carbon::now()->year);
        
        $this->loadSchedules();
    }

    public function saveSchedule()
    {
        // Validasi input
        $this->validate([
            'selectedUser' => 'required|exists:users,id',
            'selectedShift' => 'required|exists:shifts,id',
            'selectedMonth' => 'required|integer|min:1|max:12',
            'selectedYear' => 'required|integer|min:2000',
        ]);
    
        // Cek apakah bulan yang dipilih sudah lewat untuk tahun ini
        $now = now();
        if ($this->selectedYear == $now->year && $this->selectedMonth < $now->month) {
            $this->addError('selectedMonth', 'Tidak bisa memilih bulan yang telah lewat di tahun ini.');
            return;
        }
    
        // Menentukan tanggal awal dan akhir bulan yang dipilih
        $startDate = Carbon::create($this->selectedYear, $this->selectedMonth, 1);
        $endDate = $startDate->copy()->endOfMonth();
    
        // Array untuk bulan dalam format teks
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    
        // Menyusun data untuk disimpan
        $data = [];
        for ($day = 1; $day <= $startDate->daysInMonth; $day++) {
            $currentDate = $startDate->copy()->day($day);
            $dayOfWeek = $currentDate->dayOfWeek;
    
            // Hanya menyimpan jadwal untuk Senin-Jumat
            if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                // Cek apakah jadwal sudah ada untuk tanggal ini dan user ini
                $exists = Schedule::where('user_id', $this->selectedUser)
                    ->whereDate('date', $currentDate->format('Y-m-d'))
                    ->exists();
    
                if (!$exists) {
                    $data[] = [
                        'user_id' => $this->selectedUser,
                        'shift_id' => $this->selectedShift,
                        'date' => $currentDate->toDateString(),
                        'day' => $currentDate->format('l'),
                        'month' => $this->selectedMonth, 
                        'year' => $this->selectedYear,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }
    
        // Simpan jadwal jika ada data baru
        if (count($data)) {
            Schedule::insert($data);
            session()->flash('message', 'Jadwal berhasil disimpan!');
        } else {
            session()->flash('message', 'Semua jadwal sudah ada, tidak ada yang ditambahkan.');
        }
    
        // Menyimpan bulan dan tahun ke session
        session()->put('selectedMonth', $this->selectedMonth);
        session()->put('selectedYear', $this->selectedYear);
    
        // Muat ulang jadwal
        $this->loadSchedules();
    }
    
    public function loadSchedules()
    {
        $this->schedules = Schedule::where('month', $this->selectedMonth)
            ->where('year', $this->selectedYear)
            ->orderBy('date')
            ->get();

        $this->schedules = \App\Models\Schedule::with(['user', 'shift'])->get();
    }
    
    public function updatedSelectedMonth()
    {
        // Menyimpan nilai bulan ke session ketika bulan dipilih
        session()->put('selectedMonth', $this->selectedMonth);
        $this->loadSchedules();
    }

    public function updatedSelectedYear()
    {
        // Menyimpan nilai tahun ke session ketika tahun dipilih
        session()->put('selectedYear', $this->selectedYear);
        $this->loadSchedules();
    }

    public function deleteSchedule($month, $year)
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    
        $deletedSchedules = Schedule::where('month', $month)
            ->where('year', $year)
            ->delete();
        
        if ($deletedSchedules) {
            session()->flash('message', 'Jadwal untuk bulan ' . ($months[$month] ?? $month) . ' ' . $year . ' berhasil dihapus!');
        } else {
            session()->flash('message', 'Tidak ada jadwal yang ditemukan untuk bulan dan tahun ini.');
        }
    
        $this->loadSchedules();
    }

    public function showDetail($userId, $month, $year)
{
    // Ambil jadwal berdasarkan user_id, bulan, dan tahun
    $this->detailSchedules = Schedule::where('user_id', $userId)
        ->where('month', $month)
        ->where('year', $year)
        ->with(['shift'])
        ->orderBy('date')
        ->get();
}
    
public function closeDetailModal()
{
    $this->detailSchedules = [];
}

    public function render()
    {
        return view('livewire.admin.master-data.schedule-manager-component');
    }

    public function shift()
{
    return $this->belongsTo(Shift::class);
}
}
