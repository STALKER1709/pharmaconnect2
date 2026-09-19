<?php

namespace App\Models;

use App\Enums\OperateurMobileMoney;
use App\Enums\PaiementStatut;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
    protected $fillable = [
        'commande_id', 'reference', 'operateur', 'montant', 'devise',
        'statut', 'numero_payeur', 'numero_paye', 'reponse_brute', 'paye_at',
    ];

    protected function casts(): array
    {
        return [
            'operateur' => OperateurMobileMoney::class,
            'statut' => PaiementStatut::class,
            'paye_at' => 'datetime',
        ];
    }

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class);
    }

    public function montantFormate(): string
    {
        return \App\Support\Fcfa::montant($this->montant);
    }
}
