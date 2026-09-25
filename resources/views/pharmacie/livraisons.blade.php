@extends('layouts.pharmacie')

@section('titre', 'Livraisons')

@section('contenu')
<div class="flex flex-col w-full gap-space-lg">
    <x-entete-pro titre="Livraisons & coursiers" icone="local_shipping" :sous-titre="$pharmacie->nom.' — suivi des colis confiés aux coursiers PharmaConnect'"/>

    <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left min-w-[820px]">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-label-md text-label-md">
                        <th class="py-3 px-4 rounded-l-lg">Commande</th>
                        <th class="py-3 px-4">Patient & destination</th>
                        <th class="py-3 px-4">Coursier</th>
                        <th class="py-3 px-4">Statut</th>
                        <th class="py-3 px-4 text-right rounded-r-lg">Contact</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container font-body-md text-body-md">
                    @forelse ($commandes as $commande)
                        @php $livreur = $commande->livraison->livreur; @endphp
                        <tr class="hover:bg-surface-container-low/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <a href="{{ route('pharmacie.commandes.show', $commande) }}" class="font-label-lg text-label-lg text-primary hover:underline">#{{ $commande->numero }}</a>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $commande->created_at->format('d/m H:i') }}</p>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="font-label-md text-label-md text-on-surface">{{ $commande->client?->user?->name }}</p>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ \Illuminate\Support\Str::limit($commande->adresse_livraison, 36) }}</p>
                            </td>
                            <td class="py-3.5 px-4">
                                @if ($livreur)
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center font-label-sm text-label-sm">{{ initiales($livreur->user?->name) }}</div>
                                        <div>
                                            <p class="font-label-md text-label-md text-on-surface">{{ $livreur->user?->name }}</p>
                                            <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $livreur->vehiculeLabel() }}</p>
                                        </div>
                                    </div>
                                @else
                                    <span class="font-body-sm text-body-sm text-outline">Non assigné</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4"><x-badge-statut :statut="$commande->livraison->statut"/></td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($livreur?->user)
                                        <form method="POST" action="{{ route('messagerie.demarrer', $livreur->user) }}">
                                            @csrf
                                            <button class="p-2 rounded-lg bg-surface-container hover:bg-surface-container-high text-primary inline-flex" title="Écrire au coursier"><span class="material-symbols-outlined text-[18px]">two_wheeler</span></button>
                                        </form>
                                    @endif
                                    @if ($commande->client?->user)
                                        <form method="POST" action="{{ route('messagerie.demarrer', $commande->client->user) }}">
                                            @csrf
                                            <button class="p-2 rounded-lg bg-surface-container hover:bg-surface-container-high text-primary inline-flex" title="Écrire au patient"><span class="material-symbols-outlined text-[18px]">chat</span></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-10 text-center text-on-surface-variant"><span class="material-symbols-outlined text-[36px] text-outline block mb-1">local_shipping</span>Aucune livraison pour le moment.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($commandes->hasPages())
            <div class="flex justify-end">{{ $commandes->links('partials.pagination') }}</div>
        @endif
    </div>
</div>
@endsection
