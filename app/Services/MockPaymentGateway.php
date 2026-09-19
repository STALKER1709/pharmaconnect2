<?php

namespace App\Services;

use App\Contracts\PaymentGateway;
use App\Enums\OperateurMobileMoney;

/**
 * Passerelle de paiement MOCK : valide le format du numéro camerounais
 * et simule un paiement Mobile Money (MTN MoMo / Orange Money).
 * Aucun appel réseau — 100 % local, conforme à Laragon/XAMPP.
 */
class MockPaymentGateway implements PaymentGateway
{
    public function payer(string $numeroClient, int $montant, OperateurMobileMoney $operateur, string $reference): array
    {
        if (! $this->numeroValide($numeroClient, $operateur)) {
            return [
                'succes' => false,
                'reference' => $reference,
                'message' => "Numéro {$operateur->label()} invalide : {$numeroClient}. Formats attendus : ".
                    implode(', ', $operateur->prefixes()).'xx xxx xxx.',
                'details' => ['code' => 'INVALID_NUMBER'],
            ];
        }

        if ($montant <= 0) {
            return [
                'succes' => false,
                'reference' => $reference,
                'message' => 'Montant invalide.',
                'details' => ['code' => 'INVALID_AMOUNT'],
            ];
        }

        return [
            'succes' => true,
            'reference' => $reference,
            'message' => 'Paiement de '.\App\Support\Fcfa::montant($montant).' accepté via '.$operateur->label().' (simulation locale).',
            'details' => [
                'code' => 'SUCCESS',
                'operateur' => $operateur->value,
                'montant' => $montant,
                'devise' => 'XAF',
                'numero_paye' => config('services.payment.'.($operateur === OperateurMobileMoney::MtnMomo ? 'momo_payee' : 'orange_payee')),
                'simule' => true,
            ],
        ];
    }

    public function verifier(string $reference): array
    {
        return ['statut' => 'reussi', 'message' => "Transaction {$reference} confirmée (simulation)."];
    }

    /**
     * Validation d'un numéro camerounais pour l'opérateur donné.
     */
    public function numeroValide(string $numero, OperateurMobileMoney $operateur): bool
    {
        $digits = preg_replace('/\D/', '', $numero);

        // 9 chiffres après l'indicatif 237 éventuel
        if (str_starts_with($digits, '237') && strlen($digits) === 12) {
            $digits = substr($digits, 3);
        }

        if (strlen($digits) !== 9 || ! str_starts_with($digits, '6')) {
            return false;
        }

        foreach ($operateur->prefixes() as $prefixe) {
            if (str_starts_with($digits, $prefixe)) {
                return true;
            }
        }

        return false;
    }
}
