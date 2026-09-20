@extends('layouts.app')

@section('titre', 'Paiements reçus')

@section('contenu')
<h1 class="titre-page mb-6">💰 Paiements reçus</h1>

<div class="grille grille-2 mb-6">
    <div class="carte carte-corps">
        <div class="stat-libelle">Total encaissé</div>
        <div class="stat-valeur" style="color:var(--vert-600);">{{ \App\Support\Fcfa::montant($totalRecu) }}</div>
    </div>
    <div class="carte carte-corps">
        <div class="stat-libelle">Ce mois</div>
        <div class="stat-valeur">{{ \App\Support\Fcfa::montant($totalMois) }}</div>
    </div>
</div>

<div class="carte tableau-scroll">
    <table class="tableau" style="min-width:640px;">
        <thead>
            <tr>
                <th>Date</th>
                <th>Commande</th>
                <th>Opérateur</th>
                <th>Référence</th>
                <th>Statut</th>
                <th class="texte-droit">Montant</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paiements as $paiement)
                <tr>
                    <td>{{ $paiement->paye_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    <td>{{ $paiement->commande?->numero }}</td>
                    <td>{{ $paiement->operateur->label() }}</td>
                    <td class="texte-petit texte-doux">{{ $paiement->reference }}</td>
                    <td>
                        <span class="badge {{ $paiement->statut->value === 'reussi' ? 'badge-vert' : 'badge-ambre' }}">{{ $paiement->statut->label() }}</span>
                    </td>
                    <td class="texte-droit" style="font-weight:700;">{{ $paiement->montantFormate() }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="padding:32px; text-align:center;" class="texte-doux">Aucun paiement.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $paiements->links() }}
@endsection
