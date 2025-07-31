@php
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
@endphp


<div class="py-6 px-4">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Manajemen Jadwal</h2>
    </div>

    <div>
        <!-- Form Tambah Jadwal -->
        <div class="mb-6 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <form wire:submit.prevent="saveSchedule">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    {{-- User --}}
                    <div>
                        <x-label for="user" value="User" />
                        <select id="user" wire:model="selectedUser"
                            class="form-select mt-1 block w-full bg-gray-50 text-gray-900 dark:bg-gray-700 dark:text-white">
                            <option value="">Pilih User</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="selectedUser" class="mt-1" />
                    </div>

                    {{-- Shift --}}
                    <div>
                        <x-label for="shift" value="Shift" />
                        <select id="shift" wire:model="selectedShift"
                            class="form-select mt-1 block w-full bg-gray-50 text-gray-900 dark:bg-gray-700 dark:text-white">
                            <option value="" >Pilih Shift</option>
                            @foreach ($shifts as $shift)
                                <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="selectedShift" class="mt-1" />
                    </div>

                    {{-- Bulan --}}
                    {{-- Bulan --}}
                    <div>
                        <x-label for="month" value="Bulan" />
                        <select id="month" wire:model="selectedMonth"
                            class="form-select mt-1 block w-full bg-gray-50 text-gray-900 dark:bg-gray-700 dark:text-white">
                            <option value="" disabled hidden>Pilih Bulan</option>
                            @foreach ($months as $index => $month)
                                                        @php
                                                            $isDisabled = ($selectedYear == now()->year && $index < now()->month);
                                                        @endphp
                                                        <option value="{{ $index + 0 }}" {{ $isDisabled ? 'disabled' : '' }}>
                                                            {{ $month }}
                                                        </option>
                            @endforeach
                        </select>
                        <x-input-error for="selectedMonth" class="mt-1" />
                    </div>
                    {{-- Tahun --}}
                    <div>
                        <x-label for="year" value="Tahun" />
                        <select id="year" wire:model="selectedYear"
                            class="form-select mt-1 block w-full bg-gray-50 text-gray-900 dark:bg-gray-700 dark:text-white">
                            <option value="" disabled hidden>Pilih Tahun</option>
                            @for ($year = now()->year; $year <= now()->year + 5; $year++)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                        <x-input-error for="selectedYear" class="mt-1" />
                    </div>

                </div>

                <div class="mt-6">
                    <x-button type="submit">
                        Simpan Jadwal
                    </x-button>
                </div>
            </form>
        </div>





        <!-- Notifikasi -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 mb-6">
            <form action="{{ route('admin.schedules.import') }}" method="POST" enctype="multipart/form-data"
                class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-2">
                @csrf
                <input type="file" name="file" required class="text-sm text-gray-700 dark:text-gray-300">
                <button
                    class="inline-flex items-center px-4 py-2 bg-green-500 dark:bg-green-500 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest 
                           text-black dark:text-white 
                           hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                    Import Jadwal
                </button>
            </form>
        
            <a href="{{ route('admin.schedules.export') }}" class="mt-2 sm:mt-0">
                <button
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                    Export Jadwal
                </button>
            </a>
        </div>
        
        <!-- Tabel Jadwal -->
        <div class="flex w-full bg-white dark:bg-gray-800 shadow overflow-hidden rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300">User</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Shift</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Bulan</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    @foreach ($schedules->groupBy(fn($item) => $item->user_id . '-' . $item->month . '-' . $item->year) as $group)
                                        @php
                                            $schedule = $group->first();
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                                {{ $schedule->user->name }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                                {{ $schedule->shift->name }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                                {{ $months[$schedule->month] }} {{ $schedule->year }}
                                            </td>
                                            <td class="px-6 py-4 space-x-2">
                                                <button
                                                    wire:click="showDetail('{{ $schedule->user_id }}', '{{ $schedule->month }}', '{{ $schedule->year }}')"
                                                    class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                                    Detail
                                                </button>
                                                <x-danger-button wire:click="deleteSchedule({{ $schedule->month }}, {{ $schedule->year }})">
                                                    Hapus
                                                </x-danger-button>
                                            </td>
                                        </tr>
                    @endforeach

                    @if ($schedules->isEmpty())
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
                                Belum ada jadwal.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            @if (!empty($detailSchedules))
            <div class="fixed inset-0 z-50 flex justify-center items-center bg-black bg-opacity-50">
                <div class="bg-white p-6 rounded-lg shadow-xl max-w-lg w-full overflow-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Detail Jadwal</h2>
                        <button wire:click="closeDetailModal" class="text-red-500 hover:text-red-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <ul class="space-y-2">
                        @foreach ($detailSchedules as $schedule)
                            <li class="text-sm text-gray-700">
                                <span class="font-semibold">{{ \Carbon\Carbon::parse($schedule->date)->format('d M Y') }}
                                    -</span>
                                {{ $schedule->shift->name ?? '-' }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        




        </div>
    </div>