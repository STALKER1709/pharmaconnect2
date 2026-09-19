<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Events\NouveauMessage::class => [
            \App\Listeners\NotifierNouveauMessage::class,
        ],
        \App\Events\PositionLivreurMiseAJour::class => [
            // diffusion uniquement — pas de listener persistant
        ],
        \App\Events\CommandeStatutChange::class => [
            \App\Listeners\NotifierCommandeStatut::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
