<?php

namespace App\Providers;

use App\Contracts\ChatbotService;
use App\Contracts\PaymentGateway;
use App\Contracts\SmsGateway;
use App\Contracts\TelephonyGateway;
use App\Services\Mock\MockChatbotService;
use App\Services\Mock\MockPaymentGateway;
use App\Services\Mock\MockSmsGateway;
use App\Services\Mock\MockTelephonyGateway;
use Illuminate\Support\ServiceProvider;

class ContractsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Acteurs externes : implémentations mock par défaut,
        // remplaçables via variables d'environnement (.env).
        $this->app->bind(PaymentGateway::class, function ($app) {
            return match (config('services.payment.driver', 'mock')) {
                default => new MockPaymentGateway(),
            };
        });

        $this->app->bind(SmsGateway::class, function ($app) {
            return match (config('services.sms.driver', 'mock')) {
                default => new MockSmsGateway(),
            };
        });

        $this->app->bind(TelephonyGateway::class, function ($app) {
            return match (config('services.telephony.driver', 'mock')) {
                default => new MockTelephonyGateway(),
            };
        });

        $this->app->bind(ChatbotService::class, function ($app) {
            return match (config('services.chatbot.driver', 'mock')) {
                default => new MockChatbotService(),
            };
        });
    }
}
