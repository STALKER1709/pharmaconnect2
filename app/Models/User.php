<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'telephone', 'statut',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ─── Héritage UML Utilisateur → Client / Pharmacie / Livreur ───

    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function pharmacie(): HasOne
    {
        return $this->hasOne(Pharmacie::class);
    }

    public function livreur(): HasOne
    {
        return $this->hasOne(Livreur::class);
    }

    // ─── Helpers de rôle ───

    public function aLeRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function estClient(): bool
    {
        return $this->role === 'client';
    }

    public function estPharmacie(): bool
    {
        return $this->role === 'pharmacie';
    }

    public function estLivreur(): bool
    {
        return $this->role === 'livreur';
    }

    public function estAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function estActif(): bool
    {
        return $this->statut === 'actif';
    }

    /* Aliases utilisés par les middlewares (CheckRole, etc.) */

    public function isActif(): bool
    {
        return $this->estActif();
    }

    public function isEnAttente(): bool
    {
        return $this->statut === 'en_attente';
    }

    /** Route d'accueil selon le rôle (après connexion / redirection). */
    public function homeRoute(): string
    {
        return match ($this->role) {
            'pharmacie' => 'pharmacie.dashboard',
            'livreur' => 'livreur.dashboard',
            'admin' => 'admin.dashboard',
            default => 'dashboard',
        };
    }

    /** Profil métier (clients|pharmacies|livreurs) selon le rôle. */
    public function profil(): ?\Illuminate\Database\Eloquent\Model
    {
        return match ($this->role) {
            'client' => $this->client,
            'pharmacie' => $this->pharmacie,
            'livreur' => $this->livreur,
            default => null,
        };
    }
}
