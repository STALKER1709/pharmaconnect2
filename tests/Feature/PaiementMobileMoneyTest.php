<?php

namespace Tests\Feature;

use App\Contracts\PaymentGateway;
use App\Enums\OperateurMobileMoney;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaiementMobileMoneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_numero_mtn_valide_est_accepte(): void
    {
        $resultat = app(PaymentGateway::class)->payer(
            '677123456',
            5000,
            OperateurMobileMoney::MtnMomo,
            'PAY-TEST1'
        );

        $this->assertTrue($resultat['succes']);
        $this->assertStringContainsString('5 000 FCFA', $resultat['message']);
    }

    public function test_numero_orange_invalide_est_rejete(): void
    {
        $resultat = app(PaymentGateway::class)->payer(
            '677123456', // préfixe MTN, pas Orange
            5000,
            OperateurMobileMoney::OrangeMoney,
            'PAY-TEST2'
        );

        $this->assertFalse($resultat['succes']);
        $this->assertEquals('INVALID_NUMBER', $resultat['details']['code']);
    }

    public function test_montant_negatif_rejete(): void
    {
        $resultat = app(PaymentGateway::class)->payer(
            '690123456',
            -100,
            OperateurMobileMoney::OrangeMoney,
            'PAY-TEST3'
        );

        $this->assertFalse($resultat['succes']);
    }
}
