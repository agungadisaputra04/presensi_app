<div class="w-full">
  @php
    use Illuminate\Support\Carbon;
    $sekarang = Carbon::now();
    $hour = $sekarang->format('H');
    $greeting = '';
    
    if ($hour >= 5 && $hour < 11) {
        $greeting = 'Good Morning';
    } elseif ($hour >= 11 && $hour < 15) {
        $greeting = 'Good Afternoon';
    } elseif ($hour >= 15 && $hour < 18) {
        $greeting = 'Good Evening';
    } else {
        $greeting = 'Good Night';
    }
  @endphp

  @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  <style>
  #currentMap, #map {
    min-height: 250px; 
  }
  
  @media (max-width: 640px) {
    #scanner {
      width: 100% !important; 
    }
  }
</style>
  @endpushOnce

  @pushOnce('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
      let currentMap = document.getElementById('currentMap');
      let map = document.getElementById('map');

      setTimeout(() => {
        toggleMap();
        toggleCurrentMap();
      }, 1000);

      function toggleCurrentMap() {
        const mapIsVisible = currentMap.style.display === "none";
        currentMap.style.display = mapIsVisible ? "block" : "none";
        document.querySelector('#toggleCurrentMap').innerHTML = mapIsVisible ?
          `<x-heroicon-s-chevron-up class="mr-2 h-5 w-5" />` :
          `<x-heroicon-s-chevron-down class="mr-2 h-5 w-5" />`;
      }

      function toggleMap() {
        const mapIsVisible = map.style.display === "none";
        map.style.display = mapIsVisible ? "block" : "none";
      }
    </script>
  @endpushOnce

  @if (!$isAbsence)
    <h1 class="text-xl font-semibold mb-4 animate-pulse text-gray-600 italic">
      What’s up, {{ auth()->user()->name }}? {{ $greeting }}! 😎
    </h1>

    <div class="flex items-center justify-between gap-3 text-lg font-medium text-gray-700 dark:text-gray-100 
                bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 
                px-4 py-3 rounded-lg shadow-sm transition duration-300 ease-in-out">
      <div class="flex items-center gap-3">
        <x-heroicon-o-home class="w-6 h-6 text-green-600" />
        <span>
          Scan Barcode untuk Presensi
          <span class="font-bold text-green-700">WFO</span>
        </span>
      </div>

      <div id="latlng" class="text-sm sm:text-base font-semibold text-gray-600 dark:text-gray-100 text-right">
        <div class="whitespace-nowrap">
          <strong>{{ __('Tanggal') }}:</strong>
          <span id="tanggal-jam"></span>
        </div>
      </div>
    </div>
  @endif

  @if (!$isAbsence && !$showWfhButton && !$isOnLeaveOrSick)
    <div class="flex flex-col gap-4 md:flex-row">
      {{-- Scanner code here --}}
    </div>
  @endif

  @if (!$isAbsence)
    <script src="{{ url('/assets/js/html5-qrcode.min.js') }}"></script>
  @endif

  <div class="flex flex-col gap-4 md:flex-row">
    @if (!$isAbsence)
      <div class="flex flex-col gap-4">
        <div>
          @error('shift_id')
            <x-input-error for="shift" class="mt-2" message={{ $message }} />
          @enderror
        </div>
        
        <div class="flex justify-center outline outline-gray-100 dark:outline-slate-700" wire:ignore>
          <div id="scanner" class="min-h-72 sm:min-h-96 w-72 rounded-sm outline-dashed outline-slate-500 sm:w-96"></div>
        </div>
      </div>
    @endif

    <div class="w-full">
      <h4 id="scanner-error" class="mb-3 text-lg font-semibold text-red-500 dark:text-red-400 sm:text-xl" wire:ignore></h4>
      <h4 id="scanner-result" class="mb-3 hidden text-lg font-semibold text-green-500 dark:text-green-400 sm:text-xl">
        {{ $successMsg }}
      </h4>

      @if (!is_null($currentLiveCoords))
        <div class="flex justify-between">
          <a href="{{ \App\Helpers::getGoogleMapsUrl($currentLiveCoords[0], $currentLiveCoords[1]) }}" 
             target="_blank" class="underline hover:text-blue-400">
            {{ __('Lokasi Kamu') . ': ' . $currentLiveCoords[0] . ', ' . $currentLiveCoords[1] }}
          </a>
          <button class="text-nowrap h-6" onclick="toggleCurrentMap()" id="toggleCurrentMap">
            <x-heroicon-s-chevron-down class="mr-2 h-5 w-5" />
          </button>
        </div>
      @else
        {{ __('Your location') . ': -, -' }}
      @endif
      
      <div class="my-4 h-60 w-full md:h-96" id="currentMap" wire:ignore></div>


      @if ($isScheduledToWorkToday && !$isOnLeaveOrSick)
        <div class="flex flex-col sm:flex-row justify-center items-center text-center 
                    bg-green-500 py-2 rounded-lg shadow-lg mb-4">
          @if (!$isAbsence)
            @if ($showWfhButton)
              <div class="mb-4 flex gap-2 justify-center">
                <button wire:click="handleWfhAttendance"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-3 rounded-lg flex items-center gap-2 
                               transition duration-200 ease-in-out border border-white shadow-md">
                  @if ($existingWfhAttendance && !$existingWfhAttendance->time_out)
                    Check Out WFH
                  @else
                    Check In WFH
                  @endif
                  <x-heroicon-o-home class="w-5 h-5" />
                </button>
              </div>
            @endif
          @else
            @if ($attendance && $attendance->work_mode)
              <div class="w-full px-1">
                <div class="w-full text-center text-xl font-semibold py-2 rounded-md shadow-md
                  {{ $attendance->work_mode == 'wfh' ? 'bg-blue-100 dark:bg-blue-800 text-blue-500 dark:text-white' : '' }}
                  {{ $attendance->work_mode == 'wfo' ? 'bg-blue-100 dark:bg-yellow-800 text-yellow-900 dark:text-white' : '' }}">
                  
                  @if ($attendance->work_mode == 'wfh')
                  <div class="flex justify-center items-center gap-2 text-center py-2">
                          <x-heroicon-o-check-circle class="w-6 h-6 text-green-600" />
                          <span>
                              Hi {{ Auth::user()->name }}, kamu sudah Presensi <strong>WFH</strong> hari ini.
                          </span>
                      </div>
                  @elseif ($attendance->work_mode == 'wfo')
                  <div class="flex justify-center items-center gap-2 text-center py-2">
                          <x-heroicon-o-check-circle class="w-6 h-6 text-green-600" />
                          <span>
                              Hi {{ Auth::user()->name }}, kamu sudah Presensi <strong>WFO</strong> hari ini.
                          </span>
                      </div>
                  @endif



                  @if ($attendance->time_in && !$attendance->time_out)
                    <div class="mt-4">
                      <button wire:click="{{ $attendance->work_mode == 'wfh' ? 'handleCheckoutWfh' : 'handleCheckoutWfo' }}"
                              class="bg-red-500 hover:bg-red-700 text-white px-6 py-3 rounded-lg 
                                     flex items-center gap-2 transition duration-200 ease-in-out 
                                     border border-black shadow-md mx-auto">
                        Check Out {{ strtoupper($attendance->work_mode) }}
                      </button>
                    </div>
                  @endif
                </div>
              </div>
            @endif
          @endif
        </div>

      @elseif ($isOnLeaveOrSick)
        <div class="bg-blue-500 text-white text-center py-4 rounded-md shadow-lg mb-4">
          🚫 Kamu sedang
          {{ $todayAttendance && $todayAttendance->status === 'sick' ? 'Cuti sakit' : 'Cuti' }} hari ini.
        </div>
      @else
        <div class="bg-gray-300 text-gray-800 text-center py-4 rounded-md shadow-md">
          📅 Tidak ada jadwal kerja hari ini.
        </div>
      @endif

      <div class="grid grid-cols-2 gap-3 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2">
        @if ($isScheduledToWorkToday && !$isOnLeaveOrSick)
          <div class="{{ $attendance?->status == 'late' ? 'bg-red-200 dark:bg-red-900' : 'bg-blue-200 dark:bg-blue-900' }} 
                      flex items-center justify-between rounded-md px-4 py-2 text-gray-800 dark:text-white dark:shadow-gray-700">
            <div>
              <h4 class="text-lg font-semibold md:text-xl">Presensi Masuk</h4>
              <div class="flex flex-col sm:flex-row">
                <span>
                  @if ($attendance?->time_in)
                    {{ Carbon::parse($attendance->time_in)->format('H:i:s') }}
                  @else
                    Belum Presensi
                  @endif
                </span>
                @if ($attendance?->status == 'late')
                  <span class="mx-1 hidden sm:inline-block">|</span>
                  <span>Terlambat: Ya</span>
                @endif
              </div>
            </div>
            <x-heroicon-o-arrows-pointing-in class="h-5 w-5" />
          </div>

          <button class="col-span-1 flex items-center justify-between rounded-md bg-purple-200 px-4 py-2 
                         text-gray-800 dark:bg-purple-900 dark:text-white dark:shadow-gray-700 
                         md:col-span-1 lg:col-span-1 xl:col-span-1"
                  {{ is_null($attendance?->lat_lng) ? 'disabled' : 'onclick=toggleMap()' }} id="toggleMap">
            <div>
              <h4 class="text-lg font-semibold md:text-xl">Koordinat Presensi</h4>
              <div class="flex flex-col sm:flex-row">
                @if (is_null($attendance?->lat_lng))
                  <span>Belum Presensi</span>
                @else
                  <a href="{{ \App\Helpers::getGoogleMapsUrl($attendance->latitude, $attendance->longitude) }}"
                    target="_blank" class="underline hover:text-blue-400">
                    {{ $attendance->latitude . ', ' . $attendance->longitude }}
                  </a>
                @endif
              </div>
            </div>
            <x-heroicon-o-map-pin class="h-6 w-6" />
          </button>
        @elseif ($isOnLeaveOrSick)
          <div class="col-span-2 flex justify-center items-center text-xl font-semibold 
                      text-gray-700 bg-yellow-100 dark:bg-yellow-900 dark:text-white py-4 rounded-md">
            🚫 Kamu sedang {{ $todayAttendance && $todayAttendance->status === 'sick' ? 'Cuti sakit' : 'Cuti' }} hari ini.
          </div>
        @else
          <div class="col-span-2 flex justify-center items-center text-xl font-semibold 
                      text-white dark:text-white py-4 rounded-md bg-gradient-to-r from-blue-500 to-purple-500">
            🎉 Happy day off! 🎉
          </div>
        @endif
      </div>

      <div class="my-6 h-52 w-full md:h-64 rounded-xl overflow-hidden shadow-md" id="map" wire:ignore></div>

      <hr class="my-6 border-t border-gray-300 dark:border-gray-600">

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 w-full" wire:ignore>
        <a href="{{ route('apply-leave') }}">
          <div class="flex items-center justify-center gap-3 px-5 py-4 rounded-xl
                      bg-amber-500 hover:bg-amber-600 text-white font-semibold
                      shadow-md dark:shadow-gray-700 transition">
            <x-heroicon-o-envelope-open class="w-6 h-6" />
            <span>Pengajuan Cuti</span>
          </div>
        </a>

        <a href="{{ route('attendance-history') }}">
          <div class="flex items-center justify-center gap-3 px-5 py-4 rounded-xl
                      bg-blue-500 hover:bg-blue-600 text-white font-semibold
                      shadow-md dark:shadow-gray-700 transition">
            <x-heroicon-o-clock class="w-6 h-6" />
            <span>Riwayat Presensi</span>
          </div>
        </a>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  Livewire.on('alert', ({ type, message }) => {
      Swal.fire({
          icon: type,
          title: message,
          toast: true,
          position: 'top-end',
          timer: 6000,
          showConfirmButton: false,
      });
  });

  <!-- Di bagian CSS tambahkan -->


<!-- Di bagian script Anda -->
<script>
  // Variabel global untuk menyimpan instance peta
  let currentMapInstance = null;
  let attendanceMapInstance = null;

  // Fungsi toggle dengan perbaikan
  function toggleCurrentMap() {
    const mapIsVisible = currentMap.style.display === "none";
    currentMap.style.display = mapIsVisible ? "block" : "none";
    
    if (mapIsVisible) {
      // Inisialisasi peta hanya jika belum ada
      if (!currentMapInstance && $wire.currentLiveCoords) {
        initCurrentMap($wire.currentLiveCoords);
      }
      
      // Refresh peta setelah ditampilkan
      setTimeout(() => {
        if (currentMapInstance) {
          currentMapInstance.invalidateSize();
        }
      }, 100);
    }
  }

  function toggleMap() {
    const mapIsVisible = map.style.display === "none";
    map.style.display = mapIsVisible ? "block" : "none";
    
    if (mapIsVisible && $wire.attendance?.latitude && $wire.attendance?.longitude) {
      // Inisialisasi peta hanya jika belum ada
      if (!attendanceMapInstance) {
        initAttendanceMap(
          Number($wire.attendance.latitude),
          Number($wire.attendance.longitude)
        );
      }
      
      // Refresh peta setelah ditampilkan
      setTimeout(() => {
        if (attendanceMapInstance) {
          attendanceMapInstance.invalidateSize();
        }
      }, 100);
    }
  }

  // Fungsi inisialisasi peta yang dipisah
  function initCurrentMap(coords) {
    currentMapInstance = L.map('currentMap').setView(coords, 15);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 21,
    }).addTo(currentMapInstance);
    L.marker(coords).addTo(currentMapInstance);
  }

  function initAttendanceMap(lat, lng) {
    attendanceMapInstance = L.map('map').setView([lat, lng], 15);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 21,
    }).addTo(attendanceMapInstance);
    L.marker([lat, lng]).addTo(attendanceMapInstance);
  }

  // Di dalam getLocation() - perbaikan untuk mobile
  async function getLocation() {
    if (!navigator.geolocation) {
      errorMsg.innerHTML = "Gagal mendeteksi lokasi";
      return;
    }

    const geoOptions = {
      enableHighAccuracy: true, // Lebih akurat untuk mobile
      timeout: 10000,
      maximumAge: 0 // Selalu dapatkan lokasi terbaru
    };

    try {
      const position = await new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject, geoOptions);
      });
      
      const coords = [position.coords.latitude, position.coords.longitude];
      $wire.$set('currentLiveCoords', coords);
      
      // Hanya inisialisasi jika peta sedang ditampilkan
      if (currentMap.style.display !== "none") {
        initCurrentMap(coords);
      }
      
    } catch (err) {
      console.error('Geolocation error:', err);
      // Fallback untuk mobile
      const fallbackCoords = [-6.1754, 106.8272]; // Koordinat default
      $wire.$set('currentLiveCoords', fallbackCoords);
      
      if (currentMap.style.display !== "none") {
        initCurrentMap(fallbackCoords);
      }
    }
  }
</script>
</script>
@endpush

@script
<script>
  const errorMsg = document.querySelector('#scanner-error');
  getLocation();

  async function getLocation() {
    if (navigator.geolocation) {
      const map = L.map('currentMap');
      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 21,
      }).addTo(map);

      const geoOptions = {
        enableHighAccuracy: false,
        timeout: 10000,
        maximumAge: 30000
      };

      navigator.geolocation.getCurrentPosition(
        (position) => initMap(position, map),
        (err) => handleGeoError(err),
        geoOptions
      );

      const watchId = navigator.geolocation.watchPosition(
        (position) => updatePosition(position, map),
        (err) => handleGeoError(err),
        {
          ...geoOptions,
          enableHighAccuracy: true
        }
      );

      Livewire.on('destroy', () => {
        navigator.geolocation.clearWatch(watchId);
      });

    } else {
      errorMsg.innerHTML = "Gagal mendeteksi lokasi";
    }
  }

  function initMap(position, map) {
    const coords = [position.coords.latitude, position.coords.longitude];
    $wire.$set('currentLiveCoords', coords);
    map.setView(coords, 13);
    L.marker(coords).addTo(map);
  }

  function updatePosition(position, map) {
    const coords = [position.coords.latitude, position.coords.longitude];
    $wire.$set('currentLiveCoords', coords);
    map.setView(coords);
    L.marker(coords).addTo(map).bindPopup("Posisi terkini").openPopup();
  }

  function handleGeoError(err) {
    console.error(`ERROR(${err.code}): ${err.message}`);
    const errorMessages = {
      1: 'Izinkan akses lokasi di pengaturan browser',
      2: 'Perangkat tidak dapat memperoleh lokasi',
      3: 'Timeout: Coba refresh halaman'
    };
    alert(errorMessages[err.code] || 'Error tidak diketahui');
  }
  
  if (!$wire.isAbsence) {
    const scanner = new Html5Qrcode('scanner');

    const config = {
      formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
      fps: 15,
      aspectRatio: 1,
      qrbox: {
        width: 280,
        height: 280
      },
      supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
    };

    async function startScanning() {
      if (scanner.getState() === Html5QrcodeScannerState.PAUSED) {
        return scanner.resume();
      }
      await scanner.start(
        { facingMode: "environment" },
        config,
        onScanSuccess,
      );
    }

    async function onScanSuccess(decodedText, decodedResult) {
      console.log(`Code matched = ${decodedText}`, decodedResult);

      if (scanner.getState() === Html5QrcodeScannerState.SCANNING) {
        scanner.pause(true);
      }

      if (!(await checkTime())) {
        await startScanning();
        return;
      }

      const result = await $wire.scan(decodedText);

      if (result === true) {
        return onAttendanceSuccess();
      } else if (typeof result === 'string') {
        errorMsg.innerHTML = result;
      }

      setTimeout(async () => {
        await startScanning();
      }, 500);
    }

    async function checkTime() {
      const attendance = await $wire.getAttendance();

      if (attendance) {
        const timeIn = new Date(attendance.time_in).valueOf();
        const diff = (Date.now() - timeIn) / (1000 * 3600);
        const minAttendanceTime = 1;
        console.log(`Difference = ${diff}`);
        if (diff <= minAttendanceTime) {
          const timeInStr = new Date(attendance.time_in).toLocaleTimeString([], {
            hour: 'numeric',
            minute: 'numeric',
            second: 'numeric',
            hour12: false,
          });
          const confirmation = confirm(
            `Anda baru saja absen pada ${timeInStr}, apakah ingin melanjutkan untuk absen keluar?`
          );
          return confirmation;
        }
      }
      return true;
    }

    function updateTanggalDanJam() {
      const now = new Date();
      const options = {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      };

      const formatter = new Intl.DateTimeFormat('id-ID', options);
      document.getElementById('tanggal-jam').textContent = formatter.format(now);
    }

    setInterval(updateTanggalDanJam, 1000);
    updateTanggalDanJam();

    function onAttendanceSuccess() {
      scanner.stop();
      errorMsg.innerHTML = '';
      document.querySelector('#scanner-result').classList.remove('hidden');
    }

    const observer = new MutationObserver((mutationList, observer) => {
      const classes = ['text-white', 'bg-blue-500', 'dark:bg-blue-400', 'rounded-md', 'px-3', 'py-1'];
      for (const mutation of mutationList) {
        if (mutation.type === 'childList') {
          const startBtn = document.querySelector('#html5-qrcode-button-camera-start');
          const stopBtn = document.querySelector('#html5-qrcode-button-camera-stop');
          const fileBtn = document.querySelector('#html5-qrcode-button-file-selection');
          const permissionBtn = document.querySelector('#html5-qrcode-button-camera-permission');

          if (startBtn) {
            startBtn.classList.add(...classes);
            stopBtn.classList.add(...classes, 'bg-red-500');
            fileBtn.classList.add(...classes);
          }

          if (permissionBtn)
            permissionBtn.classList.add(...classes);
        }
      }
    });

    observer.observe(document.querySelector('#scanner'), {
      childList: true,
      subtree: true,
    });

    startScanning();

    const map = L.map('map').setView([
      Number({{ $attendance?->latitude }}),
      Number({{ $attendance?->longitude }}),
    ], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 21,
    }).addTo(map);
    L.marker([
      Number({{ $attendance?->latitude }}),
      Number({{ $attendance?->longitude }}),
    ]).addTo(map);
  }
</script>
@endscript