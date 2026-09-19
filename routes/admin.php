<?php

use App\Http\Controllers\Admin\ParametreController;
use App\Http\Controllers\Admin\StatistiqueController;
use App\Http\Controllers\Admin\UtilisateurController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', \App\Http\Controllers\Admin\DashboardController::class)->name('dashboard');

        // Validation des comptes en attente (pharmacies/livreurs)
        Route::get('/comptes-en-attente', [UtilisateurController::class, 'enAttente'])->name('comptes.en-attente');
        Route::post('/utilisateurs/{user}/valider', [UtilisateurController::class, 'valider'])->name('utilisateurs.valider');
        Route::post('/utilisateurs/{user}/suspendre', [UtilisateurController::class, 'suspendre'])->name('utilisateurs.suspendre');
        Route::post('/utilisateurs/{user}/reactiver', [UtilisateurController::class, 'reactiver'])->name('utilisateurs.reactiver');

        // Gestion des utilisateurs
        Route::get('/utilisateurs', [UtilisateurController::class, 'index'])->name('utilisateurs.index');
        Route::get('/utilisateurs/{user}', [UtilisateurController::class, 'show'])->name('utilisateurs.show');

        // Toutes les commandes
        Route::get('/commandes', [\App\Http\Controllers\Admin\CommandeController::class, 'index'])->name('commandes.index');
        Route::get('/commandes/{commande}', [\App\Http\Controllers\Admin\CommandeController::class, 'show'])->name('commandes.show');

        // Statistiques globales
        Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques');

        // Paramètres (frais de livraison…)
        Route::get('/parametres', [ParametreController::class, 'index'])->name('parametres');
        Route::post('/parametres', [ParametreController::class, 'update'])->name('parametres.update');

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications');
        Route::post('/notifications/lire', [\App\Http\Controllers\Admin\NotificationController::class, 'lireTout'])->name('notifications.lire');
    });
