<?php

namespace App\Providers;

use App\Contracts\ChatbotService;
use App\Contracts\PaymentGateway;
use App\Contracts\SmsGateway;
use App\Contracts\TelephonyGateway;
use App\Services\MockChatbotService;
use App\Services\MockPaymentGateway;
use App\Services\MockSmsGateway;
use App\Services\TelephonyGatewayTel;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        /*
         * Acteurs externes — implémentations MOCK par défaut (100 % local),
         * sélectionnées par variables .env : PAYMENT_GATEWAY, SMS_GATEWAY,
         * CHATBOT_SERVICE. Les implémentations réelles s'enregistrent ici.
         */
        $this->app->bind(PaymentGateway::class, MockPaymentGateway::class);
        $this->app->bind(SmsGateway::class, MockSmsGateway::class);
        $this->app->bind(ChatbotService::class, MockChatbotService::class);
        $this->app->bind(TelephonyGateway::class, TelephonyGatewayTel::class);
    }

    public function boot(): void
    {
        // Gate super-admin : le rôle admin passe toutes les policies
        Gate::before(fn ($user, $ability) => $user->role === 'admin' ? true : null);
    }
}
