<?php

use App\Http\Controllers\Livreur\LivraisonController;
use App\Http\Controllers\Livreur\NotificationController;
use App\Http\Controllers\Livreur\PositionLivreurController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:livreur'])
    ->prefix('livreur')
    ->name('livreur.')
    ->group(function () {
        Route::get('/', \App\Http\Controllers\Livreur\DashboardController::class)->name('dashboard');

        // Livraisons disponibles + assignées
        Route::get('/livraisons', [LivraisonController::class, 'index'])->name('livraisons.index');
        Route::get('/livraisons/{livraison}', [LivraisonController::class, 'show'])->name('livraisons.show');
        Route::post('/livraisons/{livraison}/accepter', [LivraisonController::class, 'accepter'])->name('livraisons.accepter');
        Route::post('/livraisons/{livraison}/demarrer', [LivraisonController::class, 'demarrer'])->name('livraisons.demarrer');
        Route::post('/livraisons/{livraison}/livrer', [LivraisonController::class, 'livrer'])->name('livraisons.livrer');
        Route::post('/livraisons/{livraison}/echouer', [LivraisonController::class, 'echouer'])->name('livraisons.echouer');

        // Position temps réel (Geolocation API → broadcast)
        Route::post('/position', PositionLivreurController::class)->name('position');

        // Disponibilité du livreur
        Route::post('/disponibilite', [\App\Http\Controllers\Livreur\DisponibiliteController::class, 'update'])->name('disponibilite');

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
        Route::post('/notifications/lire', [NotificationController::class, 'lireTout'])->name('notifications.lire');
    });
