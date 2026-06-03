@props([
    'title' => 'Konfirmasi',
    'subTitle' => 'Penjelasan',
    'icon' => 'fa-solid fa-circle-exclamation', // Default icon
    'iconBg' => 'bg-indigo-500', // Warna background icon
    'iconShadow' => 'shadow-indigo-200', // Warna shadow icon
    'confirmText' => 'Simpan',
    'confirmColor' => 'bg-emerald-600/90 hover:bg-emerald-700',
    'confirmAction' => '', // Aksi untuk Livewire/Alpine (contoh: wire:click="save")
    'cancelText' => 'Batal',
    'closeAction' => 'open = false', // Aksi alpine untuk menutup modal
])

<div class="bg-white min-w-2xl min-h-[60vh] mx-auto rounded-lg shadow-xl flex flex-col overflow-hidden">

    {{-- Header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 shrink-0 bg-gradient-to-r from-indigo-50 via-white to-blue-50 relative">
        <div class="absolute inset-0 opacity-40 pointer-events-none">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-200 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-blue-200 rounded-full blur-3xl"></div>
        </div>

        <div class="flex items-center gap-4 relative z-10">
            <div class="w-10 h-10 rounded-lg {{ $iconBg }} flex items-center justify-center shadow-lg {{ $iconShadow }}">
                <i class="{{ $icon }} text-white text-md"></i>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-slate-800 leading-tight">
                    {{ $title }}
                </h2>
                <div class="flex items-center gap-2 mt-1 text-sm text-slate-500">
                    <span>
                        {{ $subTitle }}
                    </span>
                </div>
            </div>
        </div>

        <button type="button" x-on:click="{{ $closeAction }}" wire:loading.attr="disabled"
            class="relative z-10 w-10 h-10 rounded-full hover:bg-red-50 text-slate-400 hover:text-red-500 transition-all duration-200 cursor-pointer">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    {{-- Main Content --}}
    <div class="relative flex-1 overflow-y-auto overflow-x-hidden p-6 min-h-0">
        {{ $slot }}
    </div>

    {{-- Footer --}}
    <div class="border-t border-slate-100 px-4 py-3 flex items-center justify-end gap-3 shrink-0 bg-slate-50">
        @if(isset($footer))
            {{ $footer }}
        @else
            <button type="button" x-on:click="{{ $closeAction }}"
                class="px-5 py-2 rounded-md border border-slate-200 bg-red-400/90 hover:bg-red-500 text-white transition cursor-pointer">
                {{ $cancelText }}
            </button>
            <button type="button" {!! $confirmAction !!}
                class="px-5 py-2 rounded-md border border-slate-200 {{ $confirmColor }} text-white transition cursor-pointer">
                {{ $confirmText }}
            </button>
        @endif
    </div>
</div>