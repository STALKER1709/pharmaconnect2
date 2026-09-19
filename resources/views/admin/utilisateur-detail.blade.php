<x-app-layout>
    <x-slot name="header">
        <x-espace-titre titre="{{ $utilisateur->nom }}" :sousTitre="$utilisateur->role->label().' — '.$utilisateur->email ?>">
            <x-slot name="actions">
                <x-badge-statut :statut="$utilisateur->statut_compte" />
            </x-slot>
        </x-espace-titre>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            @if ($commandes->isNotEmpty())
                <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h3 class="font-semibold text-slate-900">Commandes liées</h3>
                    </div>
                    @foreach ($commandes as $commande)
                        <div class="px-5 py-3 border-b border-slate-100 last:border-0 flex items-center justify-between gap-3 text-sm">
                            <div>
                                <span class="font-medium">#{{ $commande->id }}</span>
                                <span class="text-slate-500"> — {{ $commande->pharmacie->nom_commercial }} · {{ $commande->date->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span>{{ $commande->montantAvecLivraisonFormate() }}</span>
                                <x-badge-statut :statut="$commande->statut" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <aside class="space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 p-5 text-sm">
                <h3 class="font-semibold text-slate-900 mb-3">Informations</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between"><dt class="text-slate-500">Téléphone</dt><dd>{{ $utilisateur->telephone }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Sexe</dt><dd>{{ libelle_sexe($utilisateur->sexe) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Naissance</dt><dd>{{ $utilisateur->date_naissance?->format('d/m/Y') ?? '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-slate-500">Inscription</dt><dd>{{ $utilisateur->created_at->format('d/m/Y') }}</dd></div>
                </dl>

                @if ($utilisateur->client)
                    <div class="mt-3 pt-3 border-t border-slate-100">
                        <p class="text-slate-500 text-xs">Adresse client</p>
                        <p>{{ $utilisateur->client->adresse ?? 'Non renseignée' }}</p>
                    </div>
                @elseif ($utilisateur->pharmacie)
                    <div class="mt-3 pt-3 border-t border-slate-100">
                        <p class="text-slate-500 text-xs">Pharmacie</p>
                        <p class="font-medium">{{ $utilisateur->pharmacie->nom_commercial }}</p>
                        <p class="text-slate-500">{{ $utilisateur->pharmacie->adresse }}</p>
                        <p class="text-xs text-slate-400 mt-1">
                            {{ $utilisateur->pharmacie->heure_ouverture->format('H\hi') }} – {{ $utilisateur->pharmacie->heure_fermeture->format('H\hi') }}
                        </p>
                    </div>
                @elseif ($utilisateur->livreur)
                    <div class="mt-3 pt-3 border-t border-slate-100">
                        <p class="text-slate-500 text-xs">Livreur</p>
                        <p>{{ $utilisateur->livreur->disponible ? 'Disponible' : 'Indisponible' }}</p>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-5 text-sm space-y-2">
                <h3 class="font-semibold text-slate-900">Administration</h3>
                @if ($utilisateur->isEnAttente())
                    <form method="POST" action="{{ route('admin.utilisateurs.valider', $utilisateur) }}">
                        @csrf
                        <button class="w-full bg-emerald-600 text-white rounded-lg px-3 py-2 font-semibold hover:bg-emerald-700 transition">Valider ce compte</button>
                    </form>
                @elseif ($utilisateur->statut_compte === \App\Enums\StatutCompte::Actif && ! $utilisateur->isAdmin())
                    <form method="POST" action="{{ route('admin.utilisateurs.suspendre', $utilisateur) }}" onsubmit="return confirm('Suspendre ce compte ?')">
                        @csrf
                        <button class="w-full text-red-600 border border-red-200 rounded-lg px-3 py-2 font-semibold hover:bg-red-50 transition">Suspendre</button>
                    </form>
                @elseif ($utilisateur->statut_compte === \App\Enums\StatutCompte::Suspendu)
                    <form method="POST" action="{{ route('admin.utilisateurs.reactiver', $utilisateur) }}">
                        @csrf
                        <button class="w-full bg-sky-600 text-white rounded-lg px-3 py-2 font-semibold hover:bg-sky-700 transition">Réactiver</button>
                    </form>
                @else
                    <p class="text-slate-500">Compte administrateur.</p>
                @endif
            </div>
        </aside>
    </div>
</x-app-layout>
