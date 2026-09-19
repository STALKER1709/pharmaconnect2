<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Force la configuration de test indépendamment des variables
     * d'environnement du système (APP_ENV=local, SESSION_DRIVER=database…
     * injectées par l'environnement d'exécution).
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // L'environnement est mis en cache au bootstrap : on le force explicitement
        $app->detectEnvironment(fn () => 'testing');

        // Après le bootstrap : on écrase la configuration émanant de l'environnement
        $app['config']->set([
            'app.env' => 'testing',
            'app.key' => 'base64:2fl+Ktvkfl+Fuz4Qp/A75G2RTiWVA/ZoKZvp6fiiM10=',
            'session.driver' => 'array',
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'mail.default' => 'array',
            'queue.default' => 'sync',
            'cache.default' => 'array',
            'broadcasting.default' => 'null',
        ]);

        // Les singletons/casts liés à l'ancienne config doivent être rafraîchis
        $app['config']->set('app.debug', false);

        return $app;
    }
}
