<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Schedule;
use App\Models\LeaveRequest;
use Illuminate\Support\Carbon;

class NavbarNotification extends Component
{
    public $notifications = [];
    public $unreadNotificationCount = 0;
    public $showNotifications = false;

    // Interval polling (5 detik)
    protected $pollingInterval = 5;

    public function mount()
    {
        $this->loadNotifications();
    }

    // Load notifications

    public function loadNotifications()
    {
        $user = auth()->user();
        if (!$user) return;
    
        $this->notifications = [];
        $this->unreadNotificationCount = 0;
    
        $today = Carbon::today();
    
        if (!$user->is_admin) {
            $todaySchedule = Schedule::where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first();
    
            $approvedLeaveRequests = LeaveRequest::where('user_id', $user->id)
                ->where('status', 'approved')
                ->get(); // Mengambil semua data cuti yang disetujui tanpa memfilter berdasarkan tanggal
    
            // Menambahkan notifikasi untuk setiap cuti yang disetujui
            foreach ($approvedLeaveRequests as $leaveRequest) {
                // Mengonversi string menjadi objek Carbon
                $fromDate = Carbon::parse($leaveRequest->from);
    
                if ($leaveRequest->status == 'approved') {
                    if ($fromDate->isToday()) {
                        $this->notifications[] = [
                            'message' => 'Hari ini kamu cuti dan sudah disetujui.',
                            'read' => false
                        ];
                    } else {
                        $this->notifications[] = [
                            'message' => 'Cuti kamu di tanggal ' . $fromDate->format('d-m-Y') . ' sudah disetujui.',
                            'read' => false
                        ];
                    }
                }
            }
 
            // if ($today->isWeekend()) {
            //     $this->notifications[] = [
            //         'message' => 'Hari ini akhir pekan. Selamat beristirahat!',
            //         'read' => false
            //     ];
            // }
    
            // Jika ada jadwal kerja hari ini
            $today = date('Y-m-d');

            $todaySchedule = Schedule::with('shift')
                ->where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first();
            
            if ($todaySchedule && $todaySchedule->shift) {
                $start = $todaySchedule->shift->start_time;
                $end = $todaySchedule->shift->end_time;
            
                $this->notifications[] = [
                    'message' => "Hari ini kamu masuk kerja, jam $start - $end.",
                    'read' => false
                ];
            }
        }
    
        // Menghitung jumlah notifikasi yang belum dibaca
        $this->unreadNotificationCount = count(array_filter($this->notifications, function ($notification) {
            return !$notification['read']; // Hanya menghitung yang belum dibaca
        }));
    }
    
    // Mengubah status notifikasi menjadi sudah dibaca
    public function markAsRead($notificationIndex)
    {
        $this->notifications[$notificationIndex]['read'] = true;
        $this->unreadNotificationCount = count(array_filter($this->notifications, function ($notification) {
            return !$notification['read'];
        }));
    }

    public function toggleNotifications()
    {
        $this->showNotifications = !$this->showNotifications;
    }

    public function render()
    {
        return view('livewire.navbar-notification');
    }
}
