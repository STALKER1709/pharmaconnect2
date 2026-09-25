@extends('layouts.pharmacie')

@section('titre', 'Horaires & garde')

@php
    $ordre = [1, 2, 3, 4, 5, 6, 0];
    $ouverte = $pharmacie->estOuverte();
@endphp

@section('contenu')
<div class="flex flex-col w-full gap-space-lg">
    <x-entete-pro titre="Horaires d'ouverture & Garde" icone="calendar_clock" sous-titre="Le statut « Ouverte / Fermée » affiché aux patients est calculé automatiquement (heure de Douala)." :badge="$ouverte ? 'Ouverte actuellement' : null"/>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
        <form action="{{ route('pharmacie.horaires.update') }}" method="POST" class="lg:col-span-8 bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md">
            @csrf
            @method('PUT')
            <div class="flex items-center justify-between">
                <h2 class="font-headline-md text-headline-md text-on-surface">Semaine type</h2>
                <span class="px-2.5 py-1 rounded-full bg-secondary-container/20 text-on-secondary-container font-label-sm text-label-sm">Semaine {{ now()->isoWeek() }} · {{ now()->year }}</span>
            </div>
            @if ($errors->any())
                <div class="p-3 rounded-xl bg-error-container text-on-error-container font-body-sm text-body-sm">Vérifiez les heures saisies : la fermeture doit être postérieure à l'ouverture.</div>
            @endif
            <div class="flex flex-col rounded-xl overflow-hidden bg-surface-container-low/40">
                @foreach ($ordre as $i => $numero)
                    @php $horaire = $pharmacie->horaireDuJour($numero); $estAujourdhui = $numero === now()->dayOfWeek; @endphp
                    <div class="flex flex-wrap items-center justify-between gap-3 px-space-md py-3 {{ $estAujourdhui ? 'bg-secondary-container/20' : ($i % 2 ? 'bg-surface-container-lowest' : '') }}"
                         x-data="{ ouvert: {{ ($horaire?->ouvert ?? false) ? 'true' : 'false' }} }">
                        <label class="flex items-center gap-3 cursor-pointer min-w-[180px]">
                            <input type="hidden" name="jours[{{ $numero }}][ouvert]" value="0">
                            <input type="checkbox" name="jours[{{ $numero }}][ouvert]" value="1" x-model="ouvert" class="sr-only peer">
                            <span class="relative w-11 h-6 rounded-full transition-colors" :class="ouvert ? 'bg-primary' : 'bg-surface-container-high'">
                                <span class="absolute top-[2px] left-[2px] w-5 h-5 rounded-full bg-white shadow transition-transform" :class="ouvert ? 'translate-x-5' : ''"></span>
                            </span>
                            <span class="font-label-lg text-label-lg {{ $estAujourdhui ? 'text-on-surface' : 'text-on-surface-variant' }}">{{ $jours[$numero] }}@if($estAujourdhui) <span class="text-primary">(Aujourd'hui)</span>@endif</span>
                        </label>
                        <div class="flex items-center gap-2" :class="ouvert ? '' : 'opacity-40'">
                            <input type="time" name="jours[{{ $numero }}][heure_ouverture]" value="{{ $horaire?->heure_ouverture?->format('H:i') ?? '08:00' }}" class="px-3 py-1.5 rounded-lg bg-surface-container-lowest border-0 focus:ring-2 focus:ring-primary font-label-md text-label-md">
                            <span class="text-on-surface-variant">–</span>
                            <input type="time" name="jours[{{ $numero }}][heure_fermeture]" value="{{ $horaire?->heure_fermeture?->format('H:i') ?? '20:00' }}" class="px-3 py-1.5 rounded-lg bg-surface-container-lowest border-0 focus:ring-2 focus:ring-primary font-label-md text-label-md">
                            <span class="font-label-sm text-label-sm text-outline w-12 text-right" x-text="ouvert ? '' : 'Fermé'"></span>
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="font-body-sm text-body-sm text-on-surface-variant flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-primary">info</span>Garde 24h/24 : ouverture 00:00 et fermeture 23:59.</p>
            <label class="flex items-center gap-2 p-3 rounded-xl bg-surface-container-low font-body-md text-body-md text-on-surface cursor-pointer">
                <input type="checkbox" name="on_livraison" value="1" class="w-4 h-4 rounded text-primary accent-[#16a34a] focus:ring-0" @checked($pharmacie->on_livraison)>
                <span class="material-symbols-outlined text-primary text-[20px]">local_shipping</span>
                Proposer la livraison à domicile par les coursiers PharmaConnect
            </label>
            <div>
                <button class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-label-lg text-label-lg shadow-sm"><span class="material-symbols-outlined text-[18px]">save</span>Enregistrer les horaires</button>
            </div>
        </form>

        <div class="lg:col-span-4 flex flex-col gap-space-md">
            <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-3">
                <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">Statut actuel</span>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full {{ $ouverte ? 'bg-primary animate-ping' : 'bg-outline' }}"></span>
                    <span class="font-headline-md text-headline-md {{ $ouverte ? 'text-primary' : 'text-on-surface-variant' }}">{{ $ouverte ? 'Officine ouverte' : 'Officine fermée' }}</span>
                </div>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Calculé selon vos horaires et l'heure de Douala ({{ now()->format('H:i') }}).</p>
            </div>
            <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex items-start gap-3">
                <span class="material-symbols-outlined text-primary text-[22px] mt-0.5">emergency</span>
                <div>
                    <p class="font-label-lg text-label-lg text-on-surface">Permanence & garde</p>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Les officines ouvertes 24h/24 sont signalées « Garde 24h/24 » dans le catalogue public et mises en avant la nuit.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
