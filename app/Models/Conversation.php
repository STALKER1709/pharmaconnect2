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
}
