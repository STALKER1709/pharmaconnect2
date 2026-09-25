@extends('layouts.livreur')

@section('titre', 'Course '.$livraison->commande?->numero)

@section('contenu')
<div class="flex flex-col w-full">
    <div class="max-w-7xl mx-auto px-margin md:px-margin-desktop py-space-md w-full flex flex-col gap-space-lg">
        <nav class="flex items-center gap-2 font-label-md text-label-md text-on-surface-variant">
            <a href="{{ route('livreur.dashboard') }}" class="hover:text-primary flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">arrow_back</span>Missions en direct</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface font-semibold">Course #{{ $livraison->commande?->numero }}</span>
        </nav>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
            <div class="lg:col-span-8">
                @include('livreur.partials.mission', ['livraison' => $livraison])
            </div>
            <div class="lg:col-span-4 flex flex-col gap-space-md">
                <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm flex flex-col gap-3">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">receipt_long</span>
                        <h2 class="font-headline-sm text-headline-sm text-on-surface">Articles à livrer</h2>
                    </div>
                    @foreach ($livraison->commande?->lignes ?? [] as $ligne)
                        <div class="flex items-center justify-between py-1.5 border-b border-surface-container last:border-0 font-body-sm text-body-sm">
                            <span class="flex items-center gap-2 text-on-surface font-medium"><span class="material-symbols-outlined text-[16px] text-primary">medication</span>{{ $ligne->nom_medicament }}</span>
                            <span class="text-on-surface-variant font-semibold">× {{ $ligne->quantite }}</span>
                        </div>
                    @endforeach
                    <div class="flex items-center justify-between pt-2 font-label-lg text-label-lg">
                        <span class="text-on-surface-variant">Montant réglé (MoMo)</span>
                        <span class="text-primary">{{ format_fcfa($livraison->commande?->total) }}</span>
                    </div>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl p-5 shadow-sm flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-secondary-container/40 flex items-center justify-center text-primary shrink-0">
                        <span class="material-symbols-outlined text-[24px]">verified_user</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-label-md text-label-md font-bold text-on-surface">Protocole Froid &amp; Sécurité</span>
                        <span class="text-body-sm text-on-surface-variant">Vérifiez toujours le thermo-indicateur avant d'ouvrir le sac scellé devant le patient.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
