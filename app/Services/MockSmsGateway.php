<?php

namespace App\Services;

use App\Contracts\SmsGateway;
use Illuminate\Support\Facades\Log;

/**
 * Passerelle SMS MOCK : journalise le SMS dans storage/logs/laravel.log.
 * Remplaçable par une vraie passerelle via SMS_GATEWAY dans .env.
 */
class MockSmsGateway implements SmsGateway
{
    public function envoyer(string $numero, string $message): bool
    {
        Log::info("[SMS MOCK] → {$numero} : {$message}");

        return true;
    }
}
