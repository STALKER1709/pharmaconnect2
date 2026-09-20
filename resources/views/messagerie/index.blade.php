@extends('layouts.app')

@section('titre', 'Messagerie')

@section('contenu')
<h1 class="titre-page mb-6">💬 Messagerie</h1>

<div class="carte" style="overflow:hidden;">
    @forelse($conversations as $conversation)
        @php $interlocuteur = $conversation->interlocuteurPour(auth()->id()); @endphp
        <a href="{{ route('messagerie.show', $conversation) }}" class="rangée" style="padding:16px 20px; border-bottom:1px solid var(--bord); gap:16px; align-items:center; transition:background-color .15s;"
           onmouseover="this.style.background='var(--vert-50)'" onmouseout="this.style.background=''">
            <span class="avatar" style="background:var(--vert-100); color:var(--vert-700); width:44px; height:44px;">
                {{ mb_substr($interlocuteur?->name ?? '?', 0, 1) }}
            </span>
            <span style="flex:1; min-width:0;">
                <span class="rangee-entre" style="display:flex;">
                    <span style="font-weight:700; color:var(--encre);">{{ $interlocuteur?->name ?? 'Utilisateur' }}</span>
                    <span class="texte-petit texte-doux">{{ $conversation->dernier_message_at?->format('d/m H:i') }}</span>
                </span>
                @if($conversation->commande)
                    <span class="texte-petit" style="color:var(--vert-700); display:block;">📦 Commande {{ $conversation->commande->numero }}</span>
                @endif
                <span class="texte-petit texte-doux" style="display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $conversation->messages->first()?->contenu ?? 'Nouvelle conversation' }}</span>
            </span>
        </a>
    @empty
        <div class="vide">
            Aucune conversation.<br>
            @if(auth()->user()->estClient())
                Écrivez à une pharmacie depuis <a href="{{ route('public.pharmacies') }}" style="color:var(--vert-700); font-weight:600;">sa fiche</a>.
            @endif
        </div>
    @endforelse
</div>

{{ $conversations->links() }}
@endsection
