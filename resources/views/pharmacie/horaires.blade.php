@extends('layouts.app')

@section('titre', 'Horaires et statut')

@section('contenu')
<div class="mx-auto max-w-2xl space-y-6">
    <h1 class="text-2xl font-black">🕐 Horaires d'ouverture</h1>

    <form action="{{ route('pharmacie.horaires.update') }}" method="POST" class="card space-y-4 p-5">
        @csrf
        @method('PUT')

        @foreach($jours as $numero => $nom)
            @php $horaire = $pharmacie->horaireDuJour($numero); @endphp
            <div class="flex flex-wrap items-center gap-3 rounded-xl border border-menthe-50 p-3">
                <label class="flex w-36 items-center gap-2 text-sm font-semibold">
                    <input type="hidden" name="jours[{{ $numero }}][ouvert]" value="0">
                    <input type="checkbox" name="jours[{{ $numero }}][ouvert]" value="1"
                           class="h-4 w-4 rounded text-menthe-600" @checked($horaire?->ouvert ?? false)>
                    {{ $nom }}
                </label>
                <div class="flex items-center gap-2 text-sm">
                    <input type="time" name="jours[{{ $numero }}][heure_ouverture]"
                           value="{{ $horaire?->heure_ouverture?->format('H:i') ?? '08:00' }}" class="input w-28">
                    <span class="text-slate-400">→</span>
                    <input type="time" name="jours[{{ $numero }}][heure_fermeture]"
                           value="{{ $horaire?->heure_fermeture?->format('H:i') ?? '20:00' }}" class="input w-28">
                </div>
            </div>
        @endforeach

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="on_livraison" value="1" class="h-4 w-4 rounded text-menthe-600" @checked($pharmacie->on_livraison)>
            Proposer la livraison à domicile
        </label>

        <button class="btn-primary">Enregistrer les horaires</button>
    </form>

    <div class="card p-5 text-sm">
        <p class="font-semibold">Statut actuel :</p>
        <p class="mt-1">{{ $pharmacie->statutOuverture() }} — calculé automatiquement selon vos horaires (heure de Douala).</p>
    </div>
</div>
@endsection
