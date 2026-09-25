<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'client_id', 'pharmacie_user_id', 'livreur_user_id', 'commande_id', 'dernier_message_at',
    ];

    protected function casts(): array
    {
        return ['dernier_message_at' => 'datetime'];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function pharmacie(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pharmacie_user_id');
    }

    public function livreur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'livreur_user_id');
    }

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /** Les deux participants de la conversation. */
    public function participants(): array
    {
        return array_filter([
            $this->client_id,
            $this->pharmacie_user_id,
            $this->livreur_user_id,
        ]);
    }

    public function implique(int $userId): bool
    {
        return in_array($userId, $this->participants(), true);
    }

    /** Nom de l'interlocuteur pour l'utilisateur donné. */
    public function interlocuteurPour(int $userId): ?User
    {
        foreach ($this->participants() as $participantId) {
            if ($participantId !== $userId) {
                return User::find($participantId);
            }
        }

        return null;
    }

    /**
     * Carte d'identité de l'interlocuteur (liste et en-tête de la messagerie) :
     * nom affiché, sous-titre, icône Material Symbols et type.
     *
     * @return array{nom: string, sous_titre: string, icone: string, type: string, user: ?User}
     */
    public function carteInterlocuteur(int $userId): array
    {
        if ($this->pharmacie_user_id && $this->pharmacie_user_id !== $userId) {
            $profil = $this->pharmacie?->pharmacie;

            return [
                'nom' => $profil?->nom ?? $this->pharmacie?->name ?? 'Officine',
                'sous_titre' => collect([$profil?->quartier, $profil?->adresse])->filter()->unique()->implode(', ') ?: 'Officine partenaire',
                'icone' => 'local_pharmacy',
                'type' => 'Officine',
                'user' => $this->pharmacie,
            ];
        }

        if ($this->livreur_user_id && $this->livreur_user_id !== $userId) {
            $profil = $this->livreur?->livreur;

            return [
                'nom' => $this->livreur?->name ?? 'Coursier',
                'sous_titre' => collect([$profil?->vehicule ? ucfirst($profil->vehicule) : null, $profil?->immatriculation])->filter()->implode(' · ') ?: 'Coursier PharmaConnect',
                'icone' => 'two_wheeler',
                'type' => 'Coursier',
                'user' => $this->livreur,
            ];
        }

        return [
            'nom' => $this->client?->name ?? 'Patient',
            'sous_titre' => $this->commande ? 'Commande '.$this->commande->numero : 'Patient PharmaConnect',
            'icone' => 'person',
            'type' => 'Patient',
            'user' => $this->client,
        ];
    }
}
