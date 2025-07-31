@php
  use Illuminate\Support\Carbon;
  $m = Carbon::parse($month);
  $showUserDetail = !$month || $week || $date; // is week or day filter
  $isPerDayFilter = !empty($date);

  // Initialize global counters 
  $presentCount = 0;
  $lateCount = 0;
  $excusedCount = 0;
  $sickCount = 0;
  $wfhCount = 0;
  $wfoCount = 0;
@endphp

<div>
  @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  @endpushOnce
  <h3 class="col-span-2 mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
    Data Presensi
  </h3>
  <div class="mb-1 text-sm dark:text-white">Filter:</div>
  <div class="mb-4 grid grid-cols-2 flex-wrap items-center gap-5 md:gap-8 lg:flex">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
      <x-label for="month_filter" value="Per Bulan"></x-label>
      <x-input type="month" name="month_filter" id="month_filter" wire:model.live="month" />
    </div>
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
      <x-label for="week_filter" value="Per Minggu"></x-label>
      <x-input type="week" name="week_filter" id="week_filter" wire:model.live="week" />
    </div>
    <div class="col-span-2 flex flex-col gap-3 lg:flex-row lg:items-center">
      <x-label for="day_filter" value="Per Hari"></x-label>
      <x-input type="date" name="day_filter" id="day_filter" wire:model.live="date" />
    </div>
    <x-select id="division" wire:model.live="division">
      <option value="">{{ __('Select Division') }}</option>
      @foreach (App\Models\Division::all() as $_division)
        <option value="{{ $_division->id }}" {{ $_division->id == $division ? 'selected' : '' }}>
          {{ $_division->name }}
        </option>
      @endforeach
    </x-select>
    <div class="lg:col-span-2 text-sm text-gray-700 dark:text-white lg:ml-10 mt-2">
        <strong class="text-blue-500">Ket:</strong> 
        H = Hadir, T = Terlambat, C = Cuti, S = Sakit
      </div>

    
    <div class="col-span-2 flex items-center gap-2 lg:w-96">
      <x-input type="text" class="w-full" name="search" id="seacrh" wire:model="search"
        placeholder="{{ __('Search') }}" />
      <x-button type="button" wire:click="$refresh" wire:loading.attr="disabled">{{ __('Search') }}</x-button>
      @if ($search)
        <x-secondary-button type="button" wire:click="$set('search', '')" wire:loading.attr="disabled">
          {{ __('Reset') }}
        </x-secondary-button>
      @endif
    </div>
    <div class="lg:hidden"></div>
    <x-secondary-button
      href="{{ route('admin.attendances.report', ['month' => $month, 'week' => $week, 'date' => $date, 'work_mode' => $workMode,'division' => $division, 'jobTitle' => $jobTitle, ]) }}"
      class="flex justify-center gap-2">
      Cetak Laporan
      <x-heroicon-o-printer class="h-5 w-5" />
    </x-secondary-button>
  </div>
  <div class="overflow-x-scroll">
    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
      <thead class="bg-gray-50 dark:bg-gray-900">
        <tr>
          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
            {{ $showUserDetail ? __('Name') : __('Name') . '/' . __('Date') }}
          </th>
          @if ($showUserDetail)
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
              {{ __('NIP') }}
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
              {{ __('Division') }}
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
              {{ __('Job Title') }}
            </th>
            @if ($isPerDayFilter)
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
              {{ __('Work Mode') }}
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
              {{ __('Shift') }}
            </th>
          @endif
          @endif
          @foreach ($dates as $date)
            @php
              $isWeekend = $date->isSaturday() || $date->isSunday();
              $textClass = $isWeekend ? 'text-red-500 dark:text-red-300' : 'text-gray-500 dark:text-gray-300';
              $bgClass = $isWeekend ? 'bg-red-50 dark:bg-red-900' : '';
            @endphp
            <th scope="col"
              class="{{ $textClass }} {{ $bgClass }} text-nowrap border border-gray-300 px-1 py-3 text-center text-xs font-medium dark:border-gray-600">
              @if ($isPerDayFilter)
                Status
              @else
                {{ $date->format('d/m') }}
              @endif
            </th>
          @endforeach
          @if ($isPerDayFilter)
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
              {{ __('Time In') }}
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
              {{ __('Time Out') }}
            </th>
          @endif
          @if (!$isPerDayFilter)
            @foreach (['WFH', 'WFO','H', 'T', 'C', 'S'] as $_st)
                <th scope="col"
                    class="text-nowrap border border-gray-300 px-1 py-3 text-center text-xs font-medium text-gray-500 dark:border-gray-600 dark:text-gray-300">
                    {{ $_st }}
                </th>
            @endforeach
          @endif
          @if ($isPerDayFilter)
            <th scope="col" class="relative">
              <span class="sr-only">Actions</span>
            </th>
          @endif
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
        @php
          $class = 'cursor-pointer px-4 py-3 text-sm font-medium text-gray-900 dark:text-white';
        @endphp
        @foreach ($employees as $employee)
          @php
            $attendances = $employee->attendances;
            // Reset counters for each employee
            $presentCount = 0;
            $lateCount = 0;
            $excusedCount = 0;
            $sickCount = 0;
            $wfhCount = 0;
            $wfoCount = 0;
          @endphp
          <tr wire:key="{{ $employee->id }}" class="group">
            {{-- Detail karyawan --}}
            <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
              {{ $employee->name }}
            </td>
            @if ($showUserDetail)
              <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                {{ $employee->nip }}
              </td>
              <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                {{ $employee->division?->name ?? '-' }}
              </td>
              <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                {{ $employee->jobTitle?->name ?? '-' }}
              </td>
              @if ($isPerDayFilter)
              @php
                $attendance = $employee->attendances->first() ?? null;
                $workMode = $attendance['work_mode'] ?? '-';
              @endphp
              <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                {{ ucfirst($workMode) }}
              </td>
              @php
  $shiftVal = $attendance['shift'] ?? null;
  $shiftName = '-';

  if (is_object($shiftVal)) {
    $shiftName = $shiftVal->name ?? '-';
  } elseif (is_array($shiftVal)) {
    $shiftName = $shiftVal['name'] ?? '-';
  } elseif (is_string($shiftVal)) {
    $shiftName = $shiftVal;
  }
@endphp
<td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
  {{ $shiftName }}
</td>

            @endif
            @endif

            {{-- Absensi --}}
            @foreach ($dates as $date)
              @php
                $isWeekend = $date->isSaturday() || $date->isSunday();
                $attendance = $attendances->firstWhere(fn($v, $k) => $v['date'] === $date->format('Y-m-d'));
                $status = ($attendance ?? [
                    'status' => $isWeekend || !$date->isPast() ? '-' : 'absent',
                ])['status'];
                $workMode = $attendance['work_mode'] ?? null;

                // Background untuk hari weekend
                $weekendBg = $isWeekend ? 'bg-red-500 text-white dark:bg-red-500 dark:text-white' : '';

                
                // Hitung status
                switch ($status) {
                    case 'present':
                        $shortStatus = 'H';
                        $bgColor = 'bg-green-200 dark:bg-green-800 hover:bg-green-300 dark:hover:bg-green-700 border border-green-300 dark:border-green-600';
                        $presentCount++;
                        break;
                    case 'late':
                        $shortStatus = 'T';
                        $bgColor = 'bg-amber-200 dark:bg-amber-800 hover:bg-amber-300 dark:hover:bg-amber-700 border border-amber-300 dark:border-amber-600';
                        $lateCount++;
                        break;
                    case 'excused':
                        $shortStatus = 'C';
                        $bgColor = 'bg-blue-200 dark:bg-blue-800 hover:bg-blue-300 dark:hover:bg-blue-700 border border-blue-300 dark:border-blue-600';
                        $excusedCount++;
                        break;
                    case 'sick':
                        $shortStatus = 'S';
                        $bgColor = 'hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600';
                        $sickCount++;
                        break;
                    default:
                        $shortStatus = '-';
                        $bgColor = 'hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600';
                        break;
                }

                // Gabungkan warna status dan weekend
                $finalBg = $isWeekend ? $weekendBg . ' ' . $bgColor : $bgColor;
                
                // Hitung work mode
                if ($workMode === 'wfh') {
                    $wfhCount++;
                } elseif ($workMode === 'wfo') {
                    $wfoCount++;
                }
              @endphp
              @if (!$isPerDayFilter && $attendance && ($attendance['attachment'] || $attendance['note'] || $attendance['coordinates']))
    <td
      class="{{ $finalBg }} cursor-pointer text-center text-sm font-medium text-gray-900 dark:text-white">
      <button class="w-full px-1 py-3" wire:click="show({{ $attendance['id'] }})"
        onclick="setLocation({{ $attendance['lat'] ?? 0 }}, {{ $attendance['lng'] ?? 0 }})">
        @if($workMode && in_array($status, ['present', 'late', 'excused', 'sick']))
          <div class="attendance-mode flex flex-col items-center">
            <div class="status-text">{{ $shortStatus }}</div>
            <div class="mode-text !text-[8px] !leading-none !mt-[-3px]">{{ $workMode === 'wfh' ? 'WFH' : 'WFO' }}</div>
          </div>
        @else
          {{ $shortStatus }}
        @endif
      </button>
    </td>
  @else
    <td
      class="{{ $finalBg }} text-nowrap cursor-pointer px-1 py-3 text-center text-sm font-medium text-gray-900 dark:text-white">
      @if($workMode && in_array($status, ['present', 'late', 'excused', 'sick']))
        <div class="attendance-mode flex flex-col items-center">
          <div class="status-text">{{ $shortStatus }}</div>
          <div class="mode-text !text-[8px] !leading-none !mt-[-3px]">{{ $workMode === 'wfh' ? 'WFH' : 'WFO' }}</div>
        </div>
      @else
        {{ $shortStatus }}
      @endif
    </td>
  @endif
@endforeach

            {{-- Waktu masuk/keluar (harian) --}}
            @if ($isPerDayFilter)
              @php
                $attendance = $employee->attendances->isEmpty() ? null : $employee->attendances->first();
                $timeIn = $attendance ? $attendance['time_in'] : null;
                $timeOut = $attendance ? $attendance['time_out'] : null;
              @endphp
              <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                {{ $timeIn ?? '-' }}
              </td>
              <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                {{ $timeOut ?? '-' }}
              </td>
            @endif

            {{-- Total (mingguan/bulanan) --}}
            @if (!$isPerDayFilter)
            <td
                class="cursor-pointer border border-gray-300 px-1 py-3 text-center text-sm font-medium text-gray-900 group-hover:bg-gray-100 dark:border-gray-600 dark:text-white dark:group-hover:bg-gray-700">
                {{ $wfhCount }}
              </td>
              <td
                class="cursor-pointer border border-gray-300 px-1 py-3 text-center text-sm font-medium text-gray-900 group-hover:bg-gray-100 dark:border-gray-600 dark:text-white dark:group-hover:bg-gray-700">
                {{ $wfoCount }}
              </td>
              <td
                class="cursor-pointer border border-gray-300 px-1 py-3 text-center text-sm font-medium text-gray-900 group-hover:bg-gray-100 dark:border-gray-600 dark:text-white dark:group-hover:bg-gray-700">
                {{ $presentCount }}
              </td>
              <td
                class="cursor-pointer border border-gray-300 px-1 py-3 text-center text-sm font-medium text-gray-900 group-hover:bg-gray-100 dark:border-gray-600 dark:text-white dark:group-hover:bg-gray-700">
                {{ $lateCount }}
              </td>
              <td
                class="cursor-pointer border border-gray-300 px-1 py-3 text-center text-sm font-medium text-gray-900 group-hover:bg-gray-100 dark:border-gray-600 dark:text-white dark:group-hover:bg-gray-700">
                {{ $excusedCount }}
              </td>
              <td
                class="cursor-pointer border border-gray-300 px-1 py-3 text-center text-sm font-medium text-gray-900 group-hover:bg-gray-100 dark:border-gray-600 dark:text-white dark:group-hover:bg-gray-700">
                {{ $sickCount }}
              </td>

            @endif

            {{-- Action (harian) --}}
            @if ($isPerDayFilter)
              @php
                $attendance = $employee->attendances->isEmpty() ? null : $employee->attendances->first();
              @endphp
              <td
                class="cursor-pointer text-center text-sm font-medium text-gray-900 group-hover:bg-gray-100 dark:text-white dark:group-hover:bg-gray-700">
                <div class="flex items-center justify-center gap-3">
                  @if ($attendance && ($attendance['attachment'] || $attendance['note'] || $attendance['coordinates']))
                    <x-button type="button" wire:click="show({{ $attendance['id'] }})"
                      onclick="setLocation({{ $attendance['lat'] ?? 0 }}, {{ $attendance['lng'] ?? 0 }})">
                      {{ __('Detail') }}
                    </x-button>
                  @else
                    -
                  @endif
                </div>
              </td>
            @endif
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @if ($employees->isEmpty())
    <div class="my-2 text-center text-sm font-medium text-gray-900 dark:text-gray-100">
      Tidak ada data
    </div>
  @endif
  <div class="mt-3">
    {{ $employees->links() }}
  </div>

  <x-attendance-detail-modal :current-attendance="$currentAttendance" />
  @stack('attendance-detail-scripts')
</div>