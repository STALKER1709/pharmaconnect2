@extends('layouts.app')

@section('titre', 'Utilisateurs')

@section('contenu')
<div class="rangee-entre mb-6">
    <h1 class="titre-page">👥 Gestion des comptes</h1>
    <div class="rangée">
        @foreach(['en_attente' => '⏳ En attente', 'actif' => '✅ Actifs', 'suspendu' => '🚫 Suspendus', '' => 'Tous'] as $s => $label)
            <a href="{{ route('admin.utilisateurs', ['statut' => $s]) }}"
               class="badge {{ $statut === $s ? 'badge-vert' : 'badge-gris' }}" style="padding:6px 12px;">{{ $label }}</a>
        @endforeach
    </div>
</div>

<div class="carte tableau-scroll">
    <table class="tableau" style="min-width:680px;">
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Rôle</th>
                <th>Inscription</th>
                <th>Statut</th>
                <th class="texte-droit">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>
                        <div style="font-weight:600; color:var(--encre);">{{ $user->name }}</div>
                        <div class="texte-petit texte-doux">{{ $user->email }} · {{ $user->telephone }}</div>
                    </td>
                    <td>
                        @if($user->estPharmacie()) 🏥 {{ $user->pharmacie?->nom }}
                        @elseif($user->estLivreur()) 🛵 {{ $user->livreur?->vehiculeLabel() }}
                        @else 👤 Client
                        @endif
                    </td>
                    <td class="texte-petit texte-doux">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge {{ $user->statut === 'actif' ? 'badge-vert' : ($user->statut === 'en_attente' ? 'badge-ambre' : 'badge-rouge') }}">{{ ucfirst($user->statut) }}</span>
                    </td>
                    <td>
                        <div class="rangée" style="justify-content:flex-end;">
                            @if($user->statut === 'en_attente')
                                <form action="{{ route('admin.utilisateurs.valider', $user) }}" method="POST">
                                    @csrf <button class="btn btn-primaire btn-petit">Valider</button>
                                </form>
                            @endif
                            @if($user->statut === 'actif')
                                <form action="{{ route('admin.utilisateurs.suspendre', $user) }}" method="POST" onsubmit="return confirm('Suspendre ce compte ?')">
                                    @csrf <button class="btn btn-danger btn-petit">Suspendre</button>
                                </form>
                            @endif
                            @if($user->statut === 'suspendu')
                                <form action="{{ route('admin.utilisateurs.reactiver', $user) }}" method="POST">
                                    @csrf <button class="btn btn-secondaire btn-petit">Réactiver</button>
                                </form>
                            @endif
                            <a href="{{ route('admin.utilisateurs.show', $user) }}" class="btn btn-secondaire btn-petit">Voir</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="padding:32px; text-align:center;" class="texte-doux">Aucun compte pour ce filtre.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $users->links() }}
@endsection
