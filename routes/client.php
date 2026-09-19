<?php

use App\Http\Controllers\Client\AvisController;
use App\Http\Controllers\Client\ChatbotController;
use App\Http\Controllers\Client\CommandeController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\MedicamentController;
use App\Http\Controllers\Client\NotificationController;
use App\Http\Controllers\Client\PaiementController;
use App\Http\Controllers\Client\PanierController;
use App\Http\Controllers\Client\PharmacieController;
use App\Http\Controllers\Client\PositionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        // ------------------------------------------------------------------
        //  Catalogue public côté client (médicaments & pharmacies)
        // ------------------------------------------------------------------
        Route::get('/medicaments', [MedicamentController::class, 'index'])->name('medicaments.index');
        Route::get('/medicaments/{medicament}', [MedicamentController::class, 'show'])->name('medicaments.show');
        Route::get('/pharmacies', [PharmacieController::class, 'index'])->name('pharmacies.index');
        Route::get('/pharmacies/{pharmacie}', [PharmacieController::class, 'show'])->name('pharmacies.show');

        // ------------------------------------------------------------------
        //  Panier
        // ------------------------------------------------------------------
        Route::get('/panier', [PanierController::class, 'index'])->name('panier.index');
        Route::post('/panier/{medicament}', [PanierController::class, 'ajouter'])->name('panier.ajouter');
        Route::patch('/panier/{lignePanier}', [PanierController::class, 'majQuantite'])->name('panier.quantite');
        Route::delete('/panier/{lignePanier}', [PanierController::class, 'supprimer'])->name('panier.supprimer');

        // ------------------------------------------------------------------
        //  Commande (paiement inclus ; suivi + réception l'étendent)
        // ------------------------------------------------------------------
        Route::get('/commande/nouvelle', [CommandeController::class, 'create'])->name('commande.create');
        Route::post('/commandes', [CommandeController::class, 'store'])->name('commande.store');
        Route::get('/commandes', [CommandeController::class, 'index'])->name('commandes.index');
        Route::get('/commandes/{commande}', [CommandeController::class, 'show'])->name('commandes.show');
        Route::post('/commandes/{commande}/position', [CommandeController::class, 'partagerPosition'])->name('commandes.position');
        Route::post('/commandes/{commande}/confirmer-reception', [CommandeController::class, 'confirmerReception'])->name('commandes.confirmer-reception');
        Route::post('/commandes/{commande}/annuler', [CommandeController::class, 'annuler'])->name('commandes.annuler');

        // ------------------------------------------------------------------
        //  Paiement (MTN MoMo / Orange Money via mock)
        // ------------------------------------------------------------------
        Route::post('/commandes/{commande}/paiement', [PaiementController::class, 'initier'])->name('paiement.initier');
        Route::get('/paiement/simulation/{reference}', [PaiementController::class, 'simulation'])->name('paiement.simulation');
        Route::post('/paiement/simulation/{reference}/confirmer', [PaiementController::class, 'confirmer'])->name('paiement.confirmer');

        // ------------------------------------------------------------------
        //  Position du client (adresse de livraison, profil)
        // ------------------------------------------------------------------
        Route::post('/position', PositionController::class)->name('position.store');

        // ------------------------------------------------------------------
        //  Avis (pharmacie + médicament)
        // ------------------------------------------------------------------
        Route::get('/avis/creer', [AvisController::class, 'create'])->name('avis.create');
        Route::post('/avis', [AvisController::class, 'store'])->name('avis.store');
        Route::delete('/avis/{avis}', [AvisController::class, 'destroy'])->name('avis.destroy');

        // ------------------------------------------------------------------
        //  Chatbot (conseils)
        // ------------------------------------------------------------------
        Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
        Route::post('/chatbot', [ChatbotController::class, 'store'])->name('chatbot.store');

        // ------------------------------------------------------------------
        //  Notifications
        // ------------------------------------------------------------------
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
        Route::post('/notifications/lire', [NotificationController::class, 'lireTout'])->name('notifications.lire');
    });
