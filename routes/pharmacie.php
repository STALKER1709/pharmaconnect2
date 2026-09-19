<?php

use App\Http\Controllers\Pharmacie\CommandeController;
use App\Http\Controllers\Pharmacie\MedicamentController;
use App\Http\Controllers\Pharmacie\PaiementController;
use App\Http\Controllers\Pharmacie\StatistiqueController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:pharmacie'])
    ->prefix('pharmacie')
    ->name('pharmacie.')
    ->group(function () {
        Route::get('/', \App\Http\Controllers\Pharmacie\DashboardController::class)->name('dashboard');

        // ------------------------------------------------------------------
        //  Médicaments (CRUD) + stocks
        // ------------------------------------------------------------------
        Route::get('/medicaments', [MedicamentController::class, 'index'])->name('medicaments.index');
        Route::get('/medicaments/creer', [MedicamentController::class, 'create'])->name('medicaments.create');
        Route::post('/medicaments', [MedicamentController::class, 'store'])->name('medicaments.store');
        Route::get('/medicaments/{medicament}', [MedicamentController::class, 'show'])->name('medicaments.show');
        Route::get('/medicaments/{medicament}/modifier', [MedicamentController::class, 'edit'])->name('medicaments.edit');
        Route::put('/medicaments/{medicament}', [MedicamentController::class, 'update'])->name('medicaments.update');
        Route::delete('/medicaments/{medicament}', [MedicamentController::class, 'destroy'])->name('medicaments.destroy');

        // Approvisionnement rapide (stock)
        Route::post('/medicaments/{medicament}/stock', [MedicamentController::class, 'approvisionner'])->name('medicaments.stock');

        // ------------------------------------------------------------------
        //  Commandes : accepter / refuser / prête / assigner livreur
        // ------------------------------------------------------------------
        Route::get('/commandes', [CommandeController::class, 'index'])->name('commandes.index');
        Route::get('/commandes/{commande}', [CommandeController::class, 'show'])->name('commandes.show');
        Route::post('/commandes/{commande}/accepter', [CommandeController::class, 'accepter'])->name('commandes.accepter');
        Route::post('/commandes/{commande}/refuser', [CommandeController::class, 'refuser'])->name('commandes.refuser');
        Route::post('/commandes/{commande}/prete', [CommandeController::class, 'marquerPrete'])->name('commandes.prete');
        Route::post('/commandes/{commande}/assigner-livreur', [CommandeController::class, 'assignerLivreur'])->name('commandes.assigner-livreur');
        Route::post('/commandes/{commande}/valider-ordonnance', [CommandeController::class, 'validerOrdonnance'])->name('commandes.valider-ordonnance');

        // ------------------------------------------------------------------
        //  Paiements reçus
        // ------------------------------------------------------------------
        Route::get('/paiements', [PaiementController::class, 'index'])->name('paiements.index');

        // ------------------------------------------------------------------
        //  Statistiques (CA, graphiques)
        // ------------------------------------------------------------------
        Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques');

        // ------------------------------------------------------------------
        //  Horaires & statut
        // ------------------------------------------------------------------
        Route::post('/horaires', [\App\Http\Controllers\Pharmacie\HoraireController::class, 'update'])->name('horaires.update');

        // ------------------------------------------------------------------
        //  Notifications
        // ------------------------------------------------------------------
        Route::get('/notifications', [\App\Http\Controllers\Pharmacie\NotificationController::class, 'index'])->name('notifications');
        Route::post('/notifications/lire', [\App\Http\Controllers\Pharmacie\NotificationController::class, 'lireTout'])->name('notifications.lire');
    });
