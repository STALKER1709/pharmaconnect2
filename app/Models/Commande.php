<?php

namespace App\Models;

use App\Enums\CommandeStatut;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Commande extends Model
{
    /** @use HasFactory<\Database\Factories\CommandeFactory> */
    use HasFactory;

    protected $fillable = [
        'client_id', 'pharmacie_id', 'livreur_id', 'numero', 'statut',
        'sous_total', 'frais_livraison', 'total', 'adresse_livraison',
        'ville_livraison', 'latitude_livraison', 'longitude_livraison',
        'notes', 'confirmee_at', 'prete_at', 'assignee_at', 'en_livraison_at',
        'livree_at', 'annulee_at',
    ];

    protected function casts(): array
    {
        return [
            'statut' => CommandeStatut::class,
            'confirmee_at' => 'datetime',
            'prete_at' => 'datetime',
            'assignee_at' => 'datetime',
            'en_livraison_at' => 'datetime',
            'livree_at' => 'datetime',
            'annulee_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Commande $commande) {
            if (! $commande->numero) {
                // Numéro séquentiel basé sur l'ID max (évite les collisions avec les données seedées)
                $suivant = (int) Commande::max('id') + 1;
                $commande->numero = 'PC-'.date('Y').'-'.str_pad((string) $suivant, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function pharmacie(): BelongsTo
    {
        return $this->belongsTo(Pharmacie::class);
    }

    public function livreur(): BelongsTo
    {
        return $this->belongsTo(Livreur::class);
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(CommandeLigne::class);
    }

    public function paiement(): HasOne
    {
        return $this->hasOne(Paiement::class);
    }

    public function livraison(): HasOne
    {
        return $this->hasOne(Livraison::class);
    }

    public function avis(): HasMany
    {
        return $this->hasMany(Avis::class);
    }

    public function totalFormatte(): string
    {
        return \App\Support\Fcfa::montant($this->total);
    }

    public function peutEtreAnnuleeParClient(): bool
    {
        return $this->statut->annulableParClient();
    }
}
