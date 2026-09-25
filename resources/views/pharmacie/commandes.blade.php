@extends('layouts.pharmacie')

@section('titre', 'Commandes')

@php
    use App\Enums\CommandeStatut;

    $onglets = ['' => 'Toutes'] + collect([CommandeStatut::EnAttente, CommandeStatut::Confirmee, CommandeStatut::Prete, CommandeStatut::Assignee, CommandeStatut::EnLivraison, CommandeStatut::Livree])
        ->mapWithKeys(fn ($s) => [$s->value => $s->label()])->all();
@endphp

@section('contenu')
<div class="flex flex-col w-full gap-space-lg">
    <x-entete-pro titre="Commandes de l'officine" icone="receipt_long" :sous-titre="$pharmacie->nom.' — validez, préparez puis confiez les colis aux coursiers'" :badge="($compteurs['en_attente'] ?? 0) > 0 ? ($compteurs['en_attente'].' en attente') : null"/>

    <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md">
        <div class="inline-flex flex-wrap p-1 bg-surface-container-low rounded-xl gap-1 self-start">
            @foreach ($onglets as $valeur => $libelle)
                @php $actif = ($statut?->value ?? '') === $valeur; $nombre = $valeur === '' ? $compteurs->sum() : ($compteurs[$valeur] ?? 0); @endphp
                <a href="{{ route('pharmacie.commandes', array_filter(['statut' => $valeur])) }}"
                   class="px-3 py-1.5 rounded-lg font-label-md text-label-md transition-colors {{ $actif ? 'bg-surface-container-lowest text-primary shadow-sm' : 'text-on-surface-variant hover:text-on-surface' }}">
                    {{ $libelle }} <span class="{{ $actif ? 'text-primary' : 'text-outline' }}">({{ $nombre }})</span>
                </a>
            @endforeach
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left min-w-[900px]">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-label-md text-label-md">
                        <th class="py-3 px-4 rounded-l-lg">Commande</th>
                        <th class="py-3 px-4">Patient</th>
                        <th class="py-3 px-4">Articles</th>
                        <th class="py-3 px-4 text-right">Montant</th>
                        <th class="py-3 px-4">Statut</th>
                        <th class="py-3 px-4 text-right rounded-r-lg">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container font-body-md text-body-md">
                    @forelse ($commandes as $commande)
                        <tr class="hover:bg-surface-container-low/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <a href="{{ route('pharmacie.commandes.show', $commande) }}" class="font-label-lg text-label-lg text-primary hover:underline">#{{ $commande->numero }}</a>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $commande->created_at->format('d/m H:i') }}</p>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-label-md text-label-md text-on-surface">{{ $commande->client?->user?->name }}</p>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ \Illuminate\Support\Str::limit($commande->adresse_livraison, 28) }}</p>
                            </td>
                            <td class="py-3.5 px-4 font-body-sm text-body-sm text-on-surface-variant max-w-[240px]">{{ \Illuminate\Support\Str::limit($commande->lignes->map(fn ($l) => $l->quantite.'x '.$l->nom_medicament)->implode(', '), 60) }}</td>
                            <td class="py-3.5 px-4 text-right">
                                <p class="font-currency-display text-currency-display text-on-surface whitespace-nowrap">{{ format_fcfa($commande->total) }}</p>
                                <p class="font-label-sm text-label-sm text-outline">{{ $commande->paiement?->operateur?->label() }}</p>
                            </td>
                            <td class="py-3.5 px-4"><x-badge-statut :statut="$commande->statut"/></td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($commande->statut === CommandeStatut::EnAttente)
                                        <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST" onsubmit="return confirm('Refuser cette commande ?')">
                                            @csrf <input type="hidden" name="statut" value="refusee">
                                            <button class="px-3 py-1.5 rounded-lg bg-surface-container-lowest hover:bg-error-container text-error font-label-md text-label-md inline-flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">close</span>Refuser</button>
                                        </form>
                                        <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST">
                                            @csrf <input type="hidden" name="statut" value="confirmee">
                                            <button class="px-3.5 py-1.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md inline-flex items-center gap-1 shadow-sm"><span class="material-symbols-outlined text-[16px]">check</span>Accepter</button>
                                        </form>
                                    @elseif ($commande->statut === CommandeStatut::Confirmee)
                                        <form action="{{ route('pharmacie.commandes.statut', $commande) }}" method="POST">
                                            @csrf <input type="hidden" name="statut" value="prete">
                                            <button class="px-3.5 py-1.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md inline-flex items-center gap-1 shadow-sm"><span class="material-symbols-outlined text-[16px]">inventory_2</span>Marquer prête</button>
                                        </form>
                                    @elseif ($commande->statut === CommandeStatut::Prete)
                                        <a href="{{ route('pharmacie.commandes.show', $commande) }}" class="px-3.5 py-1.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md inline-flex items-center gap-1 shadow-sm"><span class="material-symbols-outlined text-[16px]">two_wheeler</span>Assigner</a>
                                    @else
                                        <a href="{{ route('pharmacie.commandes.show', $commande) }}" class="px-3 py-1.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md text-label-md inline-flex items-center gap-1">Détails<span class="material-symbols-outlined text-[16px]">chevron_right</span></a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-10 text-center text-on-surface-variant"><span class="material-symbols-outlined text-[36px] text-outline block mb-1">receipt_long</span>Aucune commande {{ $statut ? '« '.mb_strtolower($statut->label()).' »' : '' }} pour le moment.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($commandes->hasPages())
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $commandes->firstItem() }} - {{ $commandes->lastItem() }} sur {{ $commandes->total() }} commandes</span>
                {{ $commandes->links('partials.pagination') }}
            </div>
        @endif
    </div>
</div>
@endsection
