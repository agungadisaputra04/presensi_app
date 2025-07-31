@php
  use Illuminate\Support\Carbon;
  $selectedDate = Carbon::parse($date ?? ($week ?? $month))->settings(['formatFunction' => 'translatedFormat']);
  $showUserDetail = !$month || $week || $date;
  $isPerDayFilter = isset($date);
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=0.1">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Rekap Presensi | {{ $date ?? ($week ?? $month) }}</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
    }

    #table {
      border-collapse: collapse;
      width: 100%;
    }

    #table th,
    #table td {
      border: 1px solid #aaa;
      padding: 8px;
    }

    #table th {
      background-color: #f2f2f2;
    }

    #table tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    #table tr:hover {
      background-color: #f5f5f5;
    }
  </style>
</head>

<body>
  <h1>Data Presensi</h1>

  <div style="display: table; width: 100%; margin-bottom: 20px">
    <div style="display: table-cell;">
      <table>
        @if ($division)
          <tr>
            <td>Divisi</td>
            <td>:</td>
            <td>{{ App\Models\Division::find($division)->name ?? '-' }}</td>
          </tr>
        @endif
        @if ($jobTitle)
          <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ App\Models\JobTitle::find($jobTitle)->name ?? '-' }}</td>
          </tr>
        @endif
      </table>
    </div>
    <div style="display: table-cell; text-align: right;">
      @if ($month)
        Bulan: {{ $selectedDate->format('F Y') }}
      @elseif ($week)
        Tanggal: {{ $start->format('l, d/m/Y') }} - {{ $end->format('l, d/m/Y') }}
      @elseif ($date)
        Tanggal: {{ $selectedDate->format('d/m/Y') }}
      @endif
    </div>
  </div>

  <table id="table">
    <thead>
      <tr>
        <th>No.</th>
        <th>{{ $showUserDetail ? 'Nama' : 'Nama/Tanggal' }}</th>
        @if ($showUserDetail)
          <th>NIP</th>
          <th>Divisi</th>
          <th>Jabatan</th>
          @if ($isPerDayFilter)
            <th>Shift</th>
            <th>Work Mode</th>
          @endif
        @endif

        @foreach ($dates as $date)
          <th style="padding: 0px 2px; font-size: 14px">
            @if ($isPerDayFilter)
              Status
            @elseif (!$month)
              {{ $date->format('d/m') }}
            @else
              {{ $date->format('d') }}
            @endif
          </th>
        @endforeach

        @if (!$isPerDayFilter)
          <th>WFH</th>
          <th>WFO</th>
          <th>H</th>
          <th>T</th>
          <th>C</th>
          <th>S</th>
        @endif
      </tr>
    </thead>
    <tbody>
      @foreach ($employees as $employee)
        @php
          $attendances = $employee->attendances;
          $attendance = $attendances->first() ?? null;
          $presentCount = 0;
          $lateCount = 0;
          $excusedCount = 0;
          $sickCount = 0;
          $wfhCount = 0;
          $wfoCount = 0;
        @endphp
        <tr style="font-size: 12px">
          <td style="text-align: center;">{{ $loop->iteration }}</td>
          <td>{{ $employee->name }}</td>

          @if ($showUserDetail)
            <td>{{ $employee->nip }}</td>
            <td>{{ $employee->division?->name ?? '-' }}</td>
            <td>{{ $employee->jobTitle?->name ?? '-' }}</td>
            @if ($isPerDayFilter)
              <td>{{ $attendance['shift'] ?? '-' }}</td>
              <td>{{ $attendance['work_mode'] ?? '-' }}</td>
            @endif
          @endif

          @foreach ($dates as $date)
            @php
              $isWeekend = $date->isWeekend();
              $data = $attendances->firstWhere(fn($v, $k) => $v['date'] === $date->format('Y-m-d'));
              $status = $data['status'] ?? ($isWeekend || !$date->isPast() ? '-' : 'absent');
              $workMode = $data['work_mode'] ?? null;

              switch ($status) {
                case 'present':
                  $shortStatus = 'H';
                  $presentCount++;
                  break;
                case 'late':
                  $shortStatus = 'T';
                  $lateCount++;
                  break;
                case 'excused':
                  $shortStatus = 'C';
                  $excusedCount++;
                  break;
                case 'sick':
                  $shortStatus = 'S';
                  $sickCount++;
                  break;
                default:
                  $shortStatus = '-';
                  break;
              }

              if ($workMode === 'wfh') {
                $wfhCount++;
              } elseif ($workMode === 'wfo') {
                $wfoCount++;
              }
            @endphp
                      <td style="text-align: center; font-size: 12px;">
            {{ $shortStatus }}
            @if (in_array($shortStatus, ['H', 'T']) && isset($workMode))
              <div style="font-size: 10px; margin-top: -2px;">
                {{ strtoupper($workMode) }}
              </div>
            @endif
                  </td>
          @endforeach

          @if (!$isPerDayFilter)
            <td style="text-align: center;">{{ $wfhCount }}</td>
            <td style="text-align: center;">{{ $wfoCount }}</td>
            <td style="text-align: center;">{{ $presentCount }}</td>
            <td style="text-align: center;">{{ $lateCount }}</td>
            <td style="text-align: center;">{{ $excusedCount }}</td>
            <td style="text-align: center;">{{ $sickCount }}</td>
          @endif
        </tr>
      @endforeach
    </tbody>
  </table>

  @if ($employees->isEmpty())
    <div style="text-align: center; margin-top: 20px">
      Tidak ada data
    </div>
  @endif
</body>

</html>
