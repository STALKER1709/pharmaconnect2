@extends('layouts.app')

@section('titre', 'Paiements reçus')

@section('contenu')
<div class="space-y-6">
    <h1 class="text-2xl font-black">💰 Paiements reçus</h1>

    <dl class="grid gap-4 sm:grid-cols-2">
        <div class="card p-5">
            <dt class="text-sm text-slate-500">Total encaissé</dt>
            <dd class="text-2xl font-black text-menthe-700">{{ \App\Support\Fcfa::montant($totalRecu) }}</dd>
        </div>
        <div class="card p-5">
            <dt class="text-sm text-slate-500">Ce mois</dt>
            <dd class="text-2xl font-black">{{ \App\Support\Fcfa::montant($totalMois) }}</dd>
        </div>
    </dl>

    <div class="card overflow-x-auto">
        <table class="w-full min-w-[600px] text-sm">
            <thead class="bg-menthe-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Commande</th>
                    <th class="px-4 py-3">Opérateur</th>
                    <th class="px-4 py-3">Référence</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3 text-right">Montant</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-menthe-50">
                @forelse($paiements as $paiement)
                    <tr class="hover:bg-menthe-50/40">
                        <td class="px-4 py-3">{{ $paiement->paye_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $paiement->commande?->numero }}</td>
                        <td class="px-4 py-3">{{ $paiement->operateur->label() }}</td>
                        <td class="px-4 py-3 text-xs text-slate-400">{{ $paiement->reference }}</td>
                        <td class="px-4 py-3">
                            <span class="badge {{ $paiement->statut->value === 'reussi' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">{{ $paiement->statut->label() }}</span>
                        </td>
                        <td class="px-4 py-3 text-right font-bold">{{ $paiement->montantFormate() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Aucun paiement.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $paiements->links() }}
</div>
@endsection
