@extends('layouts.app')

@section('titre', 'Accueil')

@section('contenu')
<div>

    {{-- Héro Stitch --}}
    <section class="hero">
        <span class="badge badge-vert"><span class="badge-point"></span> Service pharmaceutique certifié à Douala</span>
        <h1 class="mt-4">Vos médicaments, livrés à <span class="hero-souligne">Douala</span></h1>
        <p class="hero-texte">
            Commandez directement auprès des pharmacies agréées de votre quartier.
            Payez par MTN MoMo ou Orange Money et suivez votre livreur en temps réel.
        </p>

        <form action="{{ route('accueil') }}" method="GET" class="recherche-bloc">
            <input type="search" name="q" value="{{ $recherche }}" placeholder="Rechercher un médicament, une molécule ou un symptôme…" class="champ" aria-label="Recherche">
            <span class="recherche-separateur"></span>
            <select name="categorie" class="champ" aria-label="Catégorie" onchange="this.form.submit()">
                <option value="">Toutes les catégories</option>
                @foreach($categories as $categorie)
                    <option value="{{ $categorie->id }}" {{ request('categorie') == $categorie->id ? 'selected' : '' }}>{{ $categorie->nom }}</option>
                @endforeach
            </select>
            <button class="btn btn-primaire">Rechercher 🔍</button>
        </form>

        <div class="puces-recherche">
            <span class="font-medium">Recherches fréquentes :</span>
            @foreach(['Paracétamol', 'Coartem', 'Amoxicilline', 'Vitamine C', 'Spasfon'] as $mot)
                <a class="puce-recherche" href="{{ route('accueil', ['q' => $mot]) }}">{{ $mot }}</a>
            @endforeach
        </div>

        <div class="badges-paiement">
            <span>Règlement instantané certifié :</span>
            <span class="badge badge-momo"><span class="badge-point"></span> MTN MoMo (*126#)</span>
            <span class="badge badge-orange"><span class="badge-point"></span> Orange Money (#150#)</span>
            <span class="badge badge-gris">💰 Espèces à la livraison</span>
        </div>

        <div class="rangée mt-6" style="justify-content:center; gap:32px;">
            <div class="texte-droit"><div class="stat-valeur">{{ $nbPharmacies }}</div><div class="stat-libelle">pharmacies partenaires</div></div>
            <div class="texte-droit"><div class="stat-valeur">{{ $nbMedicaments }}</div><div class="stat-libelle">références</div></div>
            <div class="texte-droit"><div class="stat-valeur">{{ $nbCommandes }}</div><div class="stat-libelle">commandes</div></div>
        </div>
    </section>

    {{-- Résultats de recherche --}}
    @if($recherche !== '')
    <section class="mb-6">
        <div class="rangee-entre mb-4">
            <h2 class="titre-section">Résultats pour « {{ $recherche }} »</h2>
            <a class="lien-voir-tout" href="{{ route('accueil') }}">Réinitialiser</a>
        </div>
        @if($medicaments->isEmpty() && $pharmacies->isEmpty())
            <div class="carte vide"><div class="vide-icone">🔎</div>Aucun résultat. Essayez un autre mot-clé.</div>
        @endif
        @if($medicaments->isNotEmpty())
        <div class="grille grille-4">
            @foreach($medicaments as $medicament)
                <a href="{{ route('public.medicament', $medicament) }}" class="carte carte-corps medic-carte">
                    <div>
                        <div class="medic-visuel">
                            <span class="medic-badge-haut badge badge-gris">{{ $medicament->categorie?->nom ?? 'Médicament' }}</span>
                            💊
                        </div>
                        <div class="medic-nom">{{ $medicament->nom }}</div>
                        <div class="medic-cat">{{ $medicament->forme }} · {{ $medicament->fabricant }}</div>
                    </div>
                </a>
            @endforeach
        </div>
        @endif
        @if($pharmacies->isNotEmpty())
        <div class="grille grille-3 mt-4">
            @foreach($pharmacies as $pharmacie)
                <a href="{{ route('public.pharmacie', $pharmacie) }}" class="carte carte-corps pharmacie-carte">
                    <div class="pharmacie-entete">
                        <div class="pharmacie-logo">🏥</div>
                        <div>
                            <div class="pharmacie-nom">{{ $pharmacie->nom }}</div>
                            <div class="pharmacie-meta">{{ $pharmacie->quartier }}, {{ $pharmacie->ville }}</div>
                        </div>
                    </div>
                    <div class="rangée texte-petit">
                        <span class="badge {{ $pharmacie->estOuverte() ? 'badge-vert' : 'badge-gris' }}">{{ $pharmacie->statutOuverture() }}</span>
                        <span>⭐ {{ number_format($pharmacie->note_moyenne, 1) }}</span>
                    </div>
                </a>
            @endforeach
        </div>
        @endif
    </section>
    @endif

    {{-- Badges de confiance --}}
    <section class="confiance mb-6">
        <div class="carte confiance-carte">
            <div class="confiance-icone">🛡️</div>
            <div>
                <h3>Pharmacies agréées</h3>
                <p>100 % de médicaments authentiques issus de pharmaciens certifiés par l'Ordre National (ONPC).</p>
            </div>
        </div>
        <div class="carte confiance-carte">
            <div class="confiance-icone bleu">📱</div>
            <div>
                <h3>Paiement Mobile Money</h3>
                <p>Paiement direct sans frais additionnels via MTN MoMo ou Orange Money à la commande.</p>
            </div>
        </div>
        <div class="carte confiance-carte">
            <div class="confiance-icone ambre">🛵</div>
            <div>
                <h3>Livraison express</h3>
                <p>Coursiers formés et équipés de sacs isothermes garantissant la chaîne du froid.</p>
            </div>
        </div>
    </section>

    {{-- Médicaments --}}
    @if($medicaments->isNotEmpty() && $recherche === '')
    <section class="mb-6">
        <div class="rangee-entre mb-4">
            <div>
                <div class="soustitre-bloc">💊 Officine numérique Douala</div>
                <h2 class="titre-section">Médicaments disponibles en officine</h2>
            </div>
            <a class="lien-voir-tout" href="{{ route('public.medicaments') }}">Tout voir le catalogue ({{ $nbMedicaments }})</a>
        </div>
        <div class="grille grille-4">
            @foreach($medicaments as $medicament)
                <a href="{{ route('public.medicament', $medicament) }}" class="carte carte-corps medic-carte">
                    <div>
                        <div class="medic-visuel">
                            <span class="medic-badge-haut badge badge-gris">{{ $medicament->categorie?->nom }}</span>
                            @if($medicament->ordonnance_obligatoire)
                                <span class="medic-badge-stock badge badge-ambre">Ordonnance</span>
                            @endif
                            💊
                        </div>
                        <div class="medic-nom">{{ $medicament->nom }}</div>
                        <div class="medic-cat">{{ $medicament->forme }} · {{ $medicament->fabricant }}</div>
                    </div>
                    <div class="medic-pied">
                        <span class="prix">{{ \App\Support\Fcfa::montant($medicament->prix_reference ?? 0) }}</span>
                        <span class="btn btn-secondaire btn-petit">Voir</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Pharmacies vedettes --}}
    @if($pharmacies->isNotEmpty())
    <section class="mb-6">
        <div class="rangee-entre mb-4">
            <h2 class="titre-section">Pharmacies vedettes</h2>
            <a class="lien-voir-tout" href="{{ route('public.pharmacies') }}">Tout voir →</a>
        </div>
        <div class="grille grille-3">
            @foreach($pharmacies as $pharmacie)
                <a href="{{ route('public.pharmacie', $pharmacie) }}" class="carte carte-corps pharmacie-carte">
                    <div class="pharmacie-entete">
                        <div class="pharmacie-logo">🏥</div>
                        <div>
                            <div class="pharmacie-nom">{{ $pharmacie->nom }}</div>
                            <div class="pharmacie-meta">{{ $pharmacie->quartier }}, {{ $pharmacie->ville }}</div>
                        </div>
                    </div>
                    <div class="rangée">
                        <span class="badge {{ $pharmacie->estOuverte() ? 'badge-vert' : 'badge-gris' }}">{{ $pharmacie->statutOuverture() }}</span>
                        <span class="texte-petit">⭐ {{ number_format($pharmacie->note_moyenne, 1) }} · 🛵 {{ \App\Support\Fcfa::montant($pharmacie->frais_livraison) }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Comment ça marche --}}
    <section class="mb-6">
        <h2 class="titre-section" style="text-align:center;">Comment ça marche ?</h2>
        <div class="grille grille-4 mt-4">
            @foreach([
                ['🔍', '1. Cherchez', 'Trouvez votre médicament ou la pharmacie la plus proche.'],
                ['🛒', '2. Commandez', 'Ajoutez au panier, partagez votre adresse (GPS ou carte).'],
                ['📱', '3. Payez', 'MTN MoMo ou Orange Money — paiement inclus à la commande.'],
                ['🛵', '4. Suivez', 'Position du livreur en temps réel, confirmation de réception.'],
            ] as $etape)
                <div class="carte carte-corps" style="text-align:center;">
                    <div style="font-size:36px;">{{ $etape[0] }}</div>
                    <h3 class="mt-2">{{ $etape[1] }}</h3>
                    <p class="mt-2 texte-petit texte-doux">{{ $etape[2] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA par rôle --}}
    <section class="grille grille-3">
        <div class="carte carte-corps">
            <h3 class="carte-titre">👤 Je suis client</h3>
            <p class="mt-2 texte-petit texte-doux">Créez votre compte gratuit et faites-vous livrer en quelques minutes.</p>
            <a href="{{ route('register') }}" class="btn btn-primaire mt-4" style="width:100%;">Créer mon compte</a>
        </div>
        <div class="carte carte-corps">
            <h3 class="carte-titre">🏥 Je suis une pharmacie</h3>
            <p class="mt-2 texte-petit texte-doux">Vendez en ligne, gérez vos stocks et recevez vos paiements Mobile Money.</p>
            <a href="{{ route('register') }}" class="btn btn-secondaire mt-4" style="width:100%;">Inscrire ma pharmacie</a>
        </div>
        <div class="carte carte-corps">
            <h3 class="carte-titre">🛵 Je suis livreur</h3>
            <p class="mt-2 texte-petit texte-doux">Rejoignez le réseau et gagnez des revenus à chaque livraison.</p>
            <a href="{{ route('register') }}" class="btn btn-secondaire mt-4" style="width:100%;">Devenir livreur</a>
        </div>
    </section>
</div>
@endsection
