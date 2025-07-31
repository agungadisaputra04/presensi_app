<div class="p-6 bg-white rounded-lg shadow-lg dark:bg-gray-900 dark:shadow-xl">
    <div class="mb-6 flex flex-col items-center gap-5 sm:flex-row md:justify-between">
        <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
            Daftar Permohonan Cuti
        </h3>
    </div>

    <!-- Only One Root Wrapper div -->
    <div>
        <!-- Desktop Table View (visible only on larger screens) -->
        <div class="overflow-x-auto rounded-lg shadow-md sm:block hidden">
            <table class="w-full table-auto divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gradient-to-r from-blue-500 to-blue-600">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-black dark:text-white">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-black dark:text-white">Dari</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-black dark:text-white">Sampai</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-black dark:text-white">Catatan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-black dark:text-white">Lampiran</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-black dark:text-white">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-black dark:text-white">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:bg-gray-800 dark:divide-gray-700">
                    @foreach ($leaveRequests as $leave)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 transition-all ease-in-out duration-300">
                            <td class="px-4 py-3 text-gray-900 dark:text-white">{{ $leave->user->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $leave->from }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $leave->to }}</td>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $leave->note }}</td>
                            <td class="px-4 py-3">
                                @if ($leave->attachment)
                                    <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="text-blue-500 hover:underline">Lihat</a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($leave->status === 'pending')
                                    <span class="text-yellow-600 dark:text-yellow-400 font-semibold">Menunggu</span>
                                @elseif ($leave->status === 'approved')
                                    <span class="text-green-600 dark:text-green-400 font-semibold">Disetujui</span>
                                @else
                                    <span class="text-red-600 dark:text-red-400 font-semibold">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 flex gap-2">
                                @if ($leave->status === 'pending')
                                    <x-button class="text-xs bg-green-500 hover:bg-green-600 text-white rounded px-3 py-2" href="{{ route('approve-leave', $leave->id) }}">
                                        Setujui
                                    </x-button>
                                    <x-danger-button class="text-xs bg-red-500 hover:bg-red-600 text-white rounded px-3 py-2" href="{{ route('reject-leave', $leave->id) }}">
                                        Tolak
                                    </x-danger-button>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500 text-sm">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (visible only on smaller screens) -->
        <div class="block sm:hidden">
            @foreach ($leaveRequests as $leave)
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h4 class="font-semibold text-lg text-gray-800 dark:text-gray-200">{{ $leave->user->name ?? '-' }}</h4>
                        <span class="text-sm text-gray-400 dark:text-gray-500">{{ $leave->status }}</span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Dari: {{ $leave->from }} Sampai: {{ $leave->to }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $leave->note }}</p>
                    @if ($leave->attachment)
                        <p class="text-sm text-blue-500 dark:text-blue-400">
                            <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="hover:underline">Lihat Lampiran</a>
                        </p>
                    @else
                        <p class="text-sm text-gray-400 dark:text-gray-500">No attachment</p>
                    @endif
                    <div class="flex items-center gap-3 mt-3">
                        @if ($leave->status === 'pending')
                            <x-button class="text-xs bg-green-500 hover:bg-green-600 text-white rounded px-3 py-2" href="{{ route('approve-leave', $leave->id) }}">Setujui</x-button>
                            <x-danger-button class="text-xs bg-red-500 hover:bg-red-600 text-white rounded px-3 py-2" href="{{ route('reject-leave', $leave->id) }}">Tolak</x-danger-button>
                        @else
                            <span class="text-gray-400 dark:text-gray-500 text-sm">Selesai</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
