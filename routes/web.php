<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Client;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Livreur;
use App\Http\Controllers\MessagerieController;
use App\Http\Controllers\PaiementController as ClientPaiementController;
use App\Http\Controllers\Pharmacie;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SuiviController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques — Visiteur
|--------------------------------------------------------------------------
*/

Route::get('/', [WelcomeController::class, 'index'])->name('accueil');
Route::get('/pharmacies', [PublicController::class, 'pharmacies'])->name('public.pharmacies');
Route::get('/pharmacies/{pharmacie}', [PublicController::class, 'pharmacie'])->name('public.pharmacie');
Route::get('/medicaments', [PublicController::class, 'medicaments'])->name('public.medicaments');
Route::get('/medicaments/{medicament}', [PublicController::class, 'medicament'])->name('public.medicament');

/*
|--------------------------------------------------------------------------
| Authentification (Breeze)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Utilisateur connecté (tous rôles) — actif uniquement
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', \App\Http\Middleware\EnsureUserActif::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');

    // Messagerie temps réel (Reverb) — tous les rôles
    Route::get('/messagerie', [MessagerieController::class, 'index'])->name('messagerie.index');
    Route::get('/messagerie/{conversation}', [MessagerieController::class, 'show'])->name('messagerie.show');
    Route::post('/messagerie/{conversation}/messages', [MessagerieController::class, 'envoyer'])->name('messagerie.envoyer');
    Route::post('/messagerie/demarrer/{user}', [MessagerieController::class, 'demarrer'])->name('messagerie.demarrer');

    // Chatbot conseils
    Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('/chatbot', [ChatbotController::class, 'demander'])->name('chatbot.demander');
    Route::delete('/chatbot', [ChatbotController::class, 'vider'])->name('chatbot.vider');

    /*
    |----------------------------------------------------------------------
    | CLIENT
    |----------------------------------------------------------------------
    */
    Route::middleware('role:client')->prefix('client')->name('client.')->group(function () {
        Route::get('/dashboard', [Client\DashboardController::class, 'index'])->name('dashboard');
    });

    // Panier (client)
    Route::middleware('role:client')->group(function () {
        Route::get('/panier', [CartController::class, 'index'])->name('panier.index');
        Route::post('/panier/ajouter/{stock}', [CartController::class, 'ajouter'])->name('panier.ajouter');
        Route::patch('/panier', [CartController::class, 'modifier'])->name('panier.modifier');
        Route::delete('/panier', [CartController::class, 'vider'])->name('panier.vider');
        Route::delete('/panier/ligne', [CartController::class, 'supprimer'])->name('panier.supprimer');

        // Passer commande (paiement INCLUS)
        Route::get('/commande/nouvelle', [CheckoutController::class, 'create'])->name('commande.create');
        Route::post('/commande', [CheckoutController::class, 'store'])->name('commande.store');

        // Commandes et suivi
        Route::get('/commandes', [CommandeController::class, 'index'])->name('commandes.index');
        Route::get('/commandes/{commande}', [CommandeController::class, 'show'])->name('commandes.show');
        Route::post('/commandes/{commande}/annuler', [CommandeController::class, 'annuler'])->name('commandes.annuler');
        Route::post('/commandes/{commande}/confirmer-reception', [CommandeController::class, 'confirmerReception'])->name('commandes.confirmer-reception');
        Route::post('/commandes/{commande}/avis', [CommandeController::class, 'storeAvis'])->name('commandes.avis');

        // Paiement Mobile Money (spécialisations MTN / Orange)
        Route::get('/commandes/{commande}/paiement', [ClientPaiementController::class, 'create'])->name('paiement.create');
        Route::post('/commandes/{commande}/paiement', [ClientPaiementController::class, 'store'])->name('paiement.store');
        Route::get('/commandes/{commande}/paiement/statut', [ClientPaiementController::class, 'statut'])->name('paiement.statut');

        // Suivi livraison temps réel + partage de position
        Route::get('/commandes/{commande}/suivi', [SuiviController::class, 'show'])->name('suivi.show');
        Route::get('/commandes/{commande}/suivi/position', [SuiviController::class, 'position'])->name('suivi.position');
        Route::post('/commandes/{commande}/suivi/position', [SuiviController::class, 'partagerPosition'])->name('suivi.partager-position');
    });

    /*
    |----------------------------------------------------------------------
    | PHARMACIE
    |----------------------------------------------------------------------
    */
    Route::middleware('role:pharmacie')->prefix('pharmacie')->name('pharmacie.')->group(function () {
        Route::get('/dashboard', [Pharmacie\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/statistiques', [Pharmacie\StatistiquesController::class, 'index'])->name('statistiques');

        // CRUD médicaments + stock
        Route::get('/medicaments', [Pharmacie\MedicamentController::class, 'index'])->name('medicaments');
        Route::post('/medicaments', [Pharmacie\MedicamentController::class, 'store'])->name('medicaments.store');
        Route::put('/stocks/{stock}', [Pharmacie\MedicamentController::class, 'update'])->name('stocks.update');
        Route::delete('/stocks/{stock}', [Pharmacie\MedicamentController::class, 'destroy'])->name('stocks.destroy');

        // Commandes
        Route::get('/commandes', [Pharmacie\CommandeController::class, 'index'])->name('commandes');
        Route::get('/commandes/{commande}', [Pharmacie\CommandeController::class, 'show'])->name('commandes.show');
        Route::post('/commandes/{commande}/statut', [Pharmacie\CommandeController::class, 'changerStatut'])->name('commandes.statut');

        // Paiements reçus
        Route::get('/paiements', [Pharmacie\PaiementController::class, 'index'])->name('paiements');

        // Livraisons
        Route::get('/livraisons', [Pharmacie\LivraisonController::class, 'index'])->name('livraisons');

        // Horaires & statut
        Route::get('/horaires', [Pharmacie\HoraireController::class, 'edit'])->name('horaires');
        Route::put('/horaires', [Pharmacie\HoraireController::class, 'update'])->name('horaires.update');
    });

    /*
    |----------------------------------------------------------------------
    | LIVREUR
    |----------------------------------------------------------------------
    */
    Route::middleware('role:livreur')->prefix('livreur')->name('livreur.')->group(function () {
        Route::get('/dashboard', [Livreur\DashboardController::class, 'index'])->name('dashboard');
        Route::post('/disponibilite', [Livreur\LivraisonController::class, 'basculerDisponibilite'])->name('disponibilite');
        Route::get('/livraisons/{livraison}', [Livreur\LivraisonController::class, 'show'])->name('livraison');
        Route::post('/livraisons/{livraison}/accepter', [Livreur\LivraisonController::class, 'accepter'])->name('livraisons.accepter');
        Route::post('/livraisons/{livraison}/demarrer', [Livreur\LivraisonController::class, 'demarrer'])->name('livraisons.demarrer');
        Route::post('/livraisons/{livraison}/arrivee', [Livreur\LivraisonController::class, 'marquerArrivee'])->name('livraisons.arrivee');
        Route::post('/livraisons/{livraison}/livrer', [Livreur\LivraisonController::class, 'livrer'])->name('livraisons.livrer');
        Route::post('/livraisons/{livraison}/position', [Livreur\LivraisonController::class, 'signalerPosition'])->name('livraisons.position');
    });

    /*
    |----------------------------------------------------------------------
    | ADMIN
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/utilisateurs', [Admin\UtilisateurController::class, 'index'])->name('utilisateurs');
        Route::get('/utilisateurs/{user}', [Admin\UtilisateurController::class, 'show'])->name('utilisateurs.show');
        Route::post('/utilisateurs/{user}/valider', [Admin\UtilisateurController::class, 'valider'])->name('utilisateurs.valider');
        Route::post('/utilisateurs/{user}/suspendre', [Admin\UtilisateurController::class, 'suspendre'])->name('utilisateurs.suspendre');
        Route::post('/utilisateurs/{user}/reactiver', [Admin\UtilisateurController::class, 'reactiver'])->name('utilisateurs.reactiver');
        Route::delete('/utilisateurs/{user}', [Admin\UtilisateurController::class, 'destroy'])->name('utilisateurs.destroy');
        Route::post('/categories', [Admin\ParametreController::class, 'storeCategorie'])->name('categories.store');
        Route::delete('/categories/{categorie}', [Admin\ParametreController::class, 'destroyCategorie'])->name('categories.destroy');
    });
});
