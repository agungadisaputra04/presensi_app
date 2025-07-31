<div>
  @php use Carbon\Carbon; @endphp

  @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  @endpushOnce
  <h3 class="col-span-2 mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
    Data Presensi
  </h3>
  <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
    <x-label for="month_filter" value="Bulan"></x-label>
    <x-input type="month" name="month_filter" id="month_filter" wire:model.live="month" />
  </div>
  <h5 class="mt-3 text-sm dark:text-gray-200">Klik pada tanggal untuk melihat detail</h5>
  <div class="mt-4 flex w-full flex-col gap-3 lg:flex-row">
    <div class="grid w-96 grid-cols-7 overflow-x-scroll dark:text-white lg:w-[36rem]">
      @foreach (['M', 'S', 'S', 'R', 'K', 'J', 'S'] as $day)
        <div
          class="{{ $day === 'M' ? 'text-red-500' : '' }} {{ $day === 'J' ? 'text-green-600 dark:text-green-500' : '' }} flex h-10 items-center justify-center border border-gray-300 text-center dark:border-gray-600">
          {{ $day }}
        </div>
      @endforeach
      @if ($start->dayOfWeek !== 0)
        @foreach (range(1, $start->dayOfWeek) as $i)
          <div class="h-14 border border-gray-300 bg-gray-100 dark:border-gray-600 dark:bg-gray-700">
          </div>
        @endforeach
      @endif
      @php
        $presentCount = 0;
        $lateCount = 0;
        $excusedCount = 0;
        $sickCount = 0;
      @endphp
      @foreach ($dates as $date)
        @php
          $isWeekend = $date->isWeekend();
          $attendance = collect($attendances)->firstWhere(fn($v) => $v['date'] === $date->format('Y-m-d'));

$leave = collect($leaveRequests)->first(function ($leaveRequest) use ($date) {
    return $leaveRequest->status === 'approved'
        && Carbon::parse($leaveRequest->from)->lessThanOrEqualTo($date)
        && Carbon::parse($leaveRequest->to)->greaterThanOrEqualTo($date);
});

if ($attendance) {
    $status = $attendance['status'];
} elseif ($leave) {
    $status = $leave->type ?? 'excused'; // default to excused
} else {
    $status = ($isWeekend || !$date->isPast()) ? '-' : 'absent';
}


          switch ($status) {
              case 'present':
                  $shortStatus = 'Hadir';
                  $bgColor =
                      'bg-green-200 dark:bg-green-800 hover:bg-green-300 dark:hover:bg-green-700 border border-green-600';
                  $presentCount++;
                  break;
              case 'late':
                  $shortStatus = 'Telat';
                  $bgColor =
                      'bg-amber-200 dark:bg-amber-800 hover:bg-amber-300 dark:hover:bg-amber-700 border border-amber-600';
                  $lateCount++;
                  break;
              case 'excused':
                  $shortStatus = 'Cuti';
                  $bgColor =
                      'bg-blue-200 dark:bg-blue-800 hover:bg-blue-300 dark:hover:bg-blue-700 border border-blue-600';
                  $excusedCount++;
                  break;
              case 'sick':
                  $shortStatus = 'Sakit';
                  $bgColor =
                      'bg-purple-200 dark:bg-purple-950 hover:bg-purple-100 dark:hover:bg-purple-700 border border-purple-600';
                  $sickCount++;
                  break;

              default:
                  $shortStatus = '-';
                  $bgColor =
                      'bg-slate-200 text-slate-600 dark:text-slate-200 dark:bg-slate-800 border border-gray-400 dark:border-gray-700';
                  break;
          }
        @endphp
     
     
        @if ($attendance && ($attendance['attachment'] || $attendance['note'] || $attendance['coordinates']))
        <button
            class="{{ $bgColor }} h-14 w-full py-1 text-center text-xs"
            wire:click="show({{ $attendance['id'] }})"
            @if (!empty($attendance['lat']) && !empty($attendance['lng']) && $attendance['status'] !== 'excused' && $attendance['status'] !== 'sick')
              onclick="setLocation({{ $attendance['lat'] }}, {{ $attendance['lng'] }})"
            @endif
          >
        <span class="{{ $date->isSunday() ? 'text-red-500' : '' }} {{ $date->isFriday() ? 'text-green-600 dark:text-green-500' : '' }}">
          {{ $date->format('d') }}
        </span>
        <br>
        {{ $shortStatus }}
        @if (!empty($attendance['work_mode']))
          <span class="block text-[10px] font-semibold text-gray-700 dark:text-gray-300">
            @if ($attendance['work_mode'] === 'wfh')
              WFH
            @elseif ($attendance['work_mode'] === 'wfo')
              WFO
            @endif
          </span>
        @endif
      </button>
    @else
      <div class="{{ $bgColor }} h-14 py-1 text-center text-xs">
        <span class="{{ $date->isSunday() ? 'text-red-500' : '' }} {{ $date->isFriday() ? 'text-green-600 dark:text-green-500' : '' }}">
          {{ $date->format('d') }}
        </span>
        <br>
        {{ $shortStatus }}
        @if (!empty($attendance['work_mode']))
          <span class="block text-[10px] font-semibold text-gray-700 dark:text-gray-300">
            @if ($attendance['work_mode'] === 'wfh')
              WFH
            @elseif ($attendance['work_mode'] === 'wfo')
              WFO
            @endif
          </span>
        @endif
      </div>
    @endif
  @endforeach

      @if ($end->dayOfWeek !== 6)
        @foreach (range(5, $end->dayOfWeek) as $i)
          <div class="h-14 border border-gray-300 bg-gray-100 dark:border-gray-600 dark:bg-gray-700"></div>
        @endforeach
      @endif
    </div>


    
    {{-- Statistik + Riwayat --}}
    <div class="w-full lg:w-[40%] flex flex-col gap-6">
      {{-- Statistik --}}
      <div class="grid grid-cols-2 gap-3 md:grid-cols-2">
        <div
          class="rounded-md bg-green-200 px-4 py-3 text-gray-800 dark:bg-green-900 dark:text-white">
          <h4 class="text-lg font-semibold">Hadir: {{ $presentCount + $lateCount }}</h4>
          <p class="text-sm">Terlambat: {{ $lateCount }}</p>
        </div>
        <div
          class="rounded-md bg-blue-200 px-4 py-3 text-gray-800 dark:bg-blue-900 dark:text-white">
          <h4 class="text-lg font-semibold">Cuti: {{ $excusedCount }}</h4>
        </div>
        <div
          class="rounded-md bg-purple-200 px-4 py-3 text-gray-800 dark:bg-purple-900 dark:text-white">
          <h4 class="text-lg font-semibold">Sakit: {{ $sickCount }}</h4>
        </div>

      </div>
  
      {{-- Riwayat Cuti --}}
      <div>
        <h3 class="mb-3 text-lg font-semibold text-gray-800 dark:text-gray-200">Riwayat Pengajuan Cuti / Sakit</h3>
  
        <div class="grid grid-cols-1 gap-4">
          @forelse ($leaveRequests as $leaveRequest)
            <div class="rounded-xl border border-gray-300 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
              <div class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-300">
                {{ \Carbon\Carbon::parse($leaveRequest->from)->translatedFormat('d M Y') }}
                -
                {{ \Carbon\Carbon::parse($leaveRequest->to)->translatedFormat('d M Y') }}
              </div>
              <div class="text-sm text-gray-700 dark:text-gray-200">
                <span class="font-semibold">Catatan:</span> {{ $leaveRequest->note ?? '-' }}
              </div>
              @if ($leaveRequest->attachment)
                <div class="mt-1 text-sm text-blue-600 hover:underline dark:text-blue-400">
                  <a href="{{ asset('storage/' . $leaveRequest->attachment) }}" target="_blank">📎 Lihat Lampiran</a>
                </div>
              @endif
              <div class="mt-3">
                @if($leaveRequest->status == 'pending')
                  <span class="inline-block rounded-full bg-yellow-200 px-4 py-1 text-sm font-bold text-yellow-900 shadow-sm dark:bg-yellow-700 dark:text-black">
                    ⏳ Pending
                  </span>
                @elseif($leaveRequest->status == 'approved')
                  <span class="inline-block rounded-full bg-green-300 px-4 py-1 text-sm font-bold text-green-900 shadow-sm dark:bg-green-600 dark:text-white">
                    ✅ Disetujui
                  </span>
                @elseif($leaveRequest->status == 'rejected')
                  <span class="inline-block rounded-full bg-red-300 px-4 py-1 text-sm font-bold text-red-900 shadow-sm dark:bg-red-700 dark:text-white">
                    ❌ Ditolak
                  </span>
                @endif
              </div>
              
            </div>
          @empty
            <p class="text-sm text-gray-600 dark:text-gray-300">Belum ada riwayat pengajuan.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>


  

  <x-attendance-detail-modal :current-attendance="$currentAttendance" />
  @stack('attendance-detail-scripts')
</div>


