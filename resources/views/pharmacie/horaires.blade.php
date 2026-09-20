@extends('layouts.app')

@section('titre', 'Horaires et statut')

@section('contenu')
<div style="max-width:680px; margin:0 auto;">
    <h1 class="titre-page mb-6">🕐 Horaires d'ouverture</h1>

    <form action="{{ route('pharmacie.horaires.update') }}" method="POST" class="carte carte-corps mb-6" style="display:grid; gap:12px;">
        @csrf
        @method('PUT')

        @foreach($jours as $numero => $nom)
            @php $horaire = $pharmacie->horaireDuJour($numero); @endphp
            <div class="rangée" style="border:1px solid var(--bord); border-radius:12px; padding:12px;">
                <label class="rangée texte-petit" style="width:150px; gap:8px; font-weight:600;">
                    <input type="hidden" name="jours[{{ $numero }}][ouvert]" value="0">
                    <input type="checkbox" name="jours[{{ $numero }}][ouvert]" value="1"
                           class="case-a-cocher" @checked($horaire?->ouvert ?? false)>
                    {{ $nom }}
                </label>
                <div class="rangée texte-petit">
                    <input type="time" name="jours[{{ $numero }}][heure_ouverture]"
                           value="{{ $horaire?->heure_ouverture?->format('H:i') ?? '08:00' }}" class="champ" style="width:120px;">
                    <span class="texte-doux">→</span>
                    <input type="time" name="jours[{{ $numero }}][heure_fermeture]"
                           value="{{ $horaire?->heure_fermeture?->format('H:i') ?? '20:00' }}" class="champ" style="width:120px;">
                </div>
            </div>
        @endforeach

        <label class="rangée texte-petit" style="gap:8px;">
            <input type="checkbox" name="on_livraison" value="1" class="case-a-cocher" @checked($pharmacie->on_livraison)>
            Proposer la livraison à domicile
        </label>

        <button class="btn btn-primaire" style="justify-self:start;">Enregistrer les horaires</button>
    </form>

    <div class="alerte alerte-info">
        <span style="font-weight:600;">Statut actuel :</span>
        {{ $pharmacie->statutOuverture() }} — calculé automatiquement selon vos horaires (heure de Douala).
    </div>
</div>
@endsection
