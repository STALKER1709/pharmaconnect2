{{-- Cloche de notifications des espaces professionnels (maquettes tableau de bord) --}}
@php
    $notifications = auth()->user()->notifications()->latest()->take(6)->get();
    $nonLues = auth()->user()->unreadNotifications()->count();
@endphp
<div class="relative" x-data="{ ouvert: false }">
    <button type="button" @click="ouvert = ! ouvert" aria-label="Notifications"
            class="{{ $classe ?? 'relative p-2.5 rounded-xl bg-surface-container-low text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-colors' }}">
        <span class="material-symbols-outlined text-[22px]">notifications</span>
        @if ($nonLues > 0)
            <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-error ring-2 ring-surface-container-lowest"></span>
        @endif
    </button>
    <div class="absolute right-0 mt-2 w-80 bg-surface-container-lowest rounded-2xl shadow-[0px_12px_32px_-4px_rgba(30,41,59,0.08)] border border-[#e2e8f0] z-50 overflow-hidden"
         x-show="ouvert" x-cloak x-transition.opacity @click.outside="ouvert = false">
        <div class="px-4 py-3 flex items-center justify-between bg-surface-container-low">
            <span class="font-label-lg text-label-lg text-on-surface">Notifications</span>
            @if ($nonLues > 0)
                <span class="px-2 py-0.5 rounded-full bg-tertiary-container text-on-tertiary-container font-label-sm text-label-sm font-bold">{{ $nonLues }} non lues</span>
            @endif
        </div>
        <div class="max-h-96 overflow-y-auto divide-y divide-surface-container">
            @forelse ($notifications as $notification)
                @php $data = $notification->data; @endphp
                <a href="{{ $data['url'] ?? '#' }}" class="block px-4 py-3 hover:bg-surface-container-low transition-colors {{ $notification->read_at ? '' : 'bg-[#f0fdf6]' }}">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-label-md text-label-md text-on-surface truncate">{{ $data['titre'] ?? 'Notification' }}</span>
                        <span class="font-body-sm text-body-sm text-outline flex-shrink-0">{{ $notification->created_at->format('d/m H:i') }}</span>
                    </div>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5 line-clamp-2">{{ $data['message'] ?? '' }}</p>
                </a>
            @empty
                <p class="px-4 py-6 text-center font-body-sm text-body-sm text-on-surface-variant">Aucune notification pour le moment.</p>
            @endforelse
        </div>
    </div>
</div>
