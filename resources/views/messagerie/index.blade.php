@extends('layouts.app')

@section('titre', 'Messagerie')

@section('contenu')
<div class="space-y-6">
    <h1 class="text-2xl font-black">💬 Messagerie</h1>

    <div class="card divide-y divide-menthe-50">
        @forelse($conversations as $conversation)
            @php $interlocuteur = $conversation->interlocuteurPour(auth()->id()); @endphp
            <a href="{{ route('messagerie.show', $conversation) }}" class="flex items-center gap-4 p-4 transition hover:bg-menthe-50/50">
                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-menthe-100 font-bold text-menthe-700">
                    {{ mb_substr($interlocuteur?->name ?? '?', 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2">
                        <span class="truncate font-bold">{{ $interlocuteur?->name ?? 'Utilisateur' }}</span>
                        <span class="shrink-0 text-xs text-slate-400">{{ $conversation->dernier_message_at?->format('d/m H:i') }}</span>
                    </div>
                    @if($conversation->commande)
                        <div class="text-xs text-menthe-700">📦 Commande {{ $conversation->commande->numero }}</div>
                    @endif
                    <p class="truncate text-sm text-slate-500">{{ $conversation->messages->first()?->contenu ?? 'Nouvelle conversation' }}</p>
                </div>
            </a>
        @empty
            <div class="p-8 text-center text-sm text-slate-500">
                Aucune conversation.<br>
                @if(auth()->user()->estClient())
                    Écrivez à une pharmacie depuis <a href="{{ route('public.pharmacies') }}" class="font-semibold text-menthe-700 hover:underline">sa fiche</a>.
                @endif
            </div>
        @endforelse
    </div>

    {{ $conversations->links() }}
</div>
@endsection
