@extends('layouts.app')

@section('titre', 'Utilisateurs')

@section('contenu')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-black">👥 Gestion des comptes</h1>
        <div class="flex gap-2">
            @foreach(['en_attente' => '⏳ En attente', 'actif' => '✅ Actifs', 'suspendu' => '🚫 Suspendus', '' => 'Tous'] as $s => $label)
                <a href="{{ route('admin.utilisateurs', ['statut' => $s]) }}"
                   class="badge {{ $statut === $s ? 'bg-menthe-600 text-white' : 'bg-white text-slate-600 ring-1 ring-menthe-100' }} px-3 py-1.5">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <div class="card overflow-x-auto">
        <table class="w-full min-w-[640px] text-sm">
            <thead class="bg-menthe-50 text-left text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-4 py-3">Utilisateur</th>
                    <th class="px-4 py-3">Rôle</th>
                    <th class="px-4 py-3">Inscription</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-menthe-50">
                @forelse($users as $user)
                    <tr class="hover:bg-menthe-50/40">
                        <td class="px-4 py-3">
                            <div class="font-semibold">{{ $user->name }}</div>
                            <div class="text-xs text-slate-400">{{ $user->email }} · {{ $user->telephone }}</div>
                        </td>
                        <td class="px-4 py-3">
                            @if($user->estPharmacie()) 🏥 {{ $user->pharmacie?->nom }}
                            @elseif($user->estLivreur()) 🛵 {{ $user->livreur?->vehiculeLabel() }}
                            @else 👤 Client
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-400">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="badge {{ $user->statut === 'actif' ? 'bg-emerald-100 text-emerald-800' : ($user->statut === 'en_attente' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-700') }}">{{ ucfirst($user->statut) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap justify-end gap-1">
                                @if($user->statut === 'en_attente')
                                    <form action="{{ route('admin.utilisateurs.valider', $user) }}" method="POST">
                                        @csrf <button class="btn-primary text-xs">Valider</button>
                                    </form>
                                @endif
                                @if($user->statut === 'actif')
                                    <form action="{{ route('admin.utilisateurs.suspendre', $user) }}" method="POST" onsubmit="return confirm('Suspendre ce compte ?')">
                                        @csrf <button class="btn-danger text-xs">Suspendre</button>
                                    </form>
                                @endif
                                @if($user->statut === 'suspendu')
                                    <form action="{{ route('admin.utilisateurs.reactiver', $user) }}" method="POST">
                                        @csrf <button class="btn-secondary text-xs">Réactiver</button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.utilisateurs.show', $user) }}" class="btn-secondary text-xs">Voir</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Aucun compte pour ce filtre.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
</div>
@endsection
