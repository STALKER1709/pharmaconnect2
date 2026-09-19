<?php

namespace App\Services\Mock;

use App\Contracts\SmsGateway;
use Illuminate\Support\Facades\Log;

/**
 * Mock SMS : journalise les messages dans storage/logs/laravel.log.
 * Pour brancher un vrai fournisseur : implémenter SmsGateway et changer
 * SMS_GATEWAY dans .env.
 */
class MockSmsGateway implements SmsGateway
{
    public function envoyer(string $telephone, string $message): bool
    {
        Log::info("[SMS mock] vers {$telephone} : {$message}");

        return true;
    }
}
