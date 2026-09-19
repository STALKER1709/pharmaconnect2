<?php

namespace App\Contracts;

use App\Enums\OperateurMobileMoney;

/**
 * Passerelle de paiement Mobile Money (MTN MoMo, Orange Money).
 * Implémentation mock par défaut : MockPaymentGateway.
 */
interface PaymentGateway
{
    /**
     * Débit de paiement d'une commande.
     *
     * @param  string  $numeroClient  Numéro Mobile Money du client (format camerounais 6xxxxxxxx)
     * @param  int  $montant  Montant en FCFA (XAF)
     * @param  OperateurMobileMoney  $operateur  MTN MoMo ou Orange Money
     * @param  string  $reference  Référence unique de la transaction
     * @return array{succes: bool, reference: string, message: string, details: array}
     */
    public function payer(string $numeroClient, int $montant, OperateurMobileMoney $operateur, string $reference): array;

    /**
     * Vérifie le statut d'une transaction.
     *
     * @return array{statut: string, message: string}
     */
    public function verifier(string $reference): array;
}
