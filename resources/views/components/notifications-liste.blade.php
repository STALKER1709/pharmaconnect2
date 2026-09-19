@props(['notifications'])

<div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
        <h3 class="font-semibold text-slate-900">Notifications</h3>
        @if ($notifications->whereNull('read_at')->isNotEmpty())
            <form method="POST" action="{{ route(auth()->user()->panneauNotifications().'.lire') }}">
                @csrf
                <button class="text-sm text-emerald-700 hover:underline font-medium">Tout marquer comme lu</button>
            </form>
        @endif
    </div>

    @forelse ($notifications as $notification)
        <div class="px-5 py-4 border-b border-slate-100 last:border-0 {{ $notification->read_at ? '' : 'bg-emerald-50/50' }}">
            <div class="flex items-start gap-3">
                <span class="mt-0.5 text-lg">
                    @php $data = $notification->data; @endphp
                    {{ match ($data['niveau'] ?? 'info') {
                        'success' => '✅', 'warning' => '⚠️', default => '🔔' } }}
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-medium text-slate-900 text-sm">{{ $data['titre'] ?? 'Notification' }}</span>
                        <span class="text-xs text-slate-400 shrink-0">{{ $notification->created_at->format('d/m H:i') }}</span>
                    </div>
                    <p class="text-sm text-slate-600 mt-0.5">{{ $data['message'] ?? '' }}</p>
                    @if (! empty($data['url']))
                        <a href="{{ $data['url'] }}" class="text-xs font-medium text-emerald-700 hover:underline mt-1 inline-block">Ouvrir</a>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="px-5 py-10 text-center text-slate-500 text-sm">Aucune notification.</div>
    @endforelse
</div>

<div class="mt-6">{{ $notifications->links() }}</div>
