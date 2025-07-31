<x-app-layout>
    {{-- Header --}}
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Permohonan Cuti') }}
        </h2>
    </x-slot>

    {{-- Main Content --}}
    <div class="py-12 bg-gray-900 dark:bg-gray-800">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-gray-600 dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Komponen Livewire --}}
                    <livewire:admin.leave-request-manager />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
