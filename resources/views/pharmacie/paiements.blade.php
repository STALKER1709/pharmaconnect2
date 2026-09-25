@extends('layouts.pharmacie')

@section('titre', 'Paiements')

@section('contenu')
<div class="flex flex-col w-full gap-space-lg">
    <x-entete-pro titre="Paiements & recouvrement" icone="payments" sous-titre="Encaissements Mobile Money des commandes en ligne (MTN MoMo, Orange Money)"/>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
        <x-stat-pro libelle="Encaissé ce mois" :valeur="format_fcfa($totalMois)" icone="account_balance_wallet" couleur-valeur="text-primary">{{ ucfirst(now()->translatedFormat('F Y')) }}</x-stat-pro>
        <x-stat-pro libelle="Total encaissé" :valeur="format_fcfa($totalRecu)" icone="savings">Depuis l'ouverture du compte</x-stat-pro>
    </div>

    <div class="bg-surface-container-lowest p-space-lg rounded-xl shadow-sm flex flex-col gap-space-md">
        <h2 class="font-headline-md text-headline-md text-on-surface">Historique des transactions</h2>
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left min-w-[760px]">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-label-md text-label-md">
                        <th class="py-3 px-4 rounded-l-lg">Date</th>
                        <th class="py-3 px-4">Commande</th>
                        <th class="py-3 px-4">Opérateur</th>
                        <th class="py-3 px-4">Référence</th>
                        <th class="py-3 px-4">Statut</th>
                        <th class="py-3 px-4 text-right rounded-r-lg">Montant</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container font-body-md text-body-md">
                    @forelse ($paiements as $paiement)
                        <tr class="hover:bg-surface-container-low/60 transition-colors">
                            <td class="py-3.5 px-4 text-on-surface-variant whitespace-nowrap">{{ $paiement->paye_at?->format('d/m/Y H:i') ?? $paiement->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3.5 px-4">
                                @if ($paiement->commande)
                                    <a href="{{ route('pharmacie.commandes.show', $paiement->commande) }}" class="font-label-lg text-label-lg text-primary hover:underline">#{{ $paiement->commande->numero }}</a>
                                    <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $paiement->commande->client?->user?->name }}</p>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-2 font-label-md text-label-md">
                                    <span class="w-3 h-3 rounded-full {{ $paiement->operateur?->value === 'orange_money' ? 'bg-orange-500' : 'bg-amber-400' }}"></span>
                                    {{ $paiement->operateur?->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-body-sm text-on-surface-variant">{{ $paiement->reference }}</td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm font-semibold whitespace-nowrap {{ $paiement->statut?->value === 'reussi' ? 'bg-secondary-container/40 text-on-secondary-container' : ($paiement->statut?->value === 'echoue' ? 'bg-error-container text-on-error-container' : 'bg-tertiary-fixed text-on-tertiary-fixed') }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>{{ $paiement->statut?->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-currency-display text-currency-display whitespace-nowrap">{{ format_fcfa($paiement->montant) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-10 text-center text-on-surface-variant"><span class="material-symbols-outlined text-[36px] text-outline block mb-1">payments</span>Aucun paiement reçu pour le moment.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($paiements->hasPages())
            <div class="flex justify-end">{{ $paiements->links('partials.pagination') }}</div>
        @endif
    </div>
</div>
@endsection
