<div class="relative">
    <!-- Ikon notifikasi -->
    <button wire:click="toggleNotifications" class="relative text-gray-800 dark:text-white focus:outline-none">
        <!-- Standard Bell Icon -->
        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8a6 6 0 00-12 0v4a2 2 0 00-2 2v2h16v-2a2 2 0 00-2-2V8z"/>
        </svg>

        <!-- Titik Merah Jika Ada Notifikasi Belum Dibaca -->
        @if ($unreadNotificationCount > 0)
            <!-- Red Circle -->
            <div class="absolute top-0 right-0 inline-block w-5 h-5 bg-red-600 text-white text-xs rounded-full flex items-center justify-center">
                {{ $unreadNotificationCount }}
            </div>
        @endif
    </button>

    <!-- Dropdown Notifikasi -->
    @if($showNotifications)
        <div class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 shadow-lg rounded-md z-50">
            @if(count($notifications) > 0)
                <!-- Kontainer untuk daftar notifikasi dengan scroll, tanpa batasan 5 -->
                <div class="p-2 space-y-2 max-h-64 overflow-y-auto" style="max-height: 250px;">
                    @foreach($notifications as $index => $notification)
                        <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded cursor-pointer" wire:click="markAsRead({{ $index }})">
                            <div class="flex items-center justify-between">
                                <p class="text-gray-800 dark:text-white text-sm">
                                    {{ $notification['message'] }}
                                </p>
                                
                                <!-- Add "New" tag if notification is unread -->
                                @if(!$notification['read'])
                                <span class="ml-2 text-xs text-white bg-red-600 px-2 py-1 rounded-full">New</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                @if(count($notifications) > 5)
                    <div class="p-2 text-xs text-gray-600 dark:text-gray-300 text-center">
                        <span>Menampilkan {{ count($notifications) }} notifikasi</span>
                    </div>
                @endif
            @else
                <div class="p-4 text-sm text-gray-600 dark:text-gray-300">Tidak ada notifikasi.</div>
            @endif
        </div>
    @endif
</div>
