# PharmaConnect 🇨🇲

Plateforme locale de mise en relation **clients / pharmacies / livreurs** au Cameroun :
commande de médicaments en ligne, paiement **Mobile Money** (MTN MoMo, Orange Money),
livraison géolocalisée en temps réel, messagerie instantanée, avis et chatbot de conseils.

- Interface **100 % française**, devise **FCFA (XAF)**, fuseau **Africa/Douala**
- Tourne **entièrement en local** (Laragon ou XAMPP) — aucun service cloud obligatoire
- Les acteurs externes (paiement, SMS, téléphonie, chatbot) sont des **mocks** activés par `.env`

## Stack

| Élément | Choix |
|---|---|
| Framework | Laravel 12 (PHP ≥ 8.2) |
| Auth | Laravel Breeze (Blade) + middleware `role:` |
| Base de données | MySQL (défaut) ou SQLite (alternative) |
| Temps réel | Laravel Reverb + Laravel Echo |
| Files d'attente | driver `database` |
| Notifications | canaux `database` + `broadcast` (+ `mail` en log local) |
| Carte | Leaflet + OpenStreetMap (npm, zéro CDN) |
| Graphiques | Chart.js (npm, zéro CDN) |
| E-mails | `MAIL_MAILER=log` → `storage/logs/laravel.log` |
| Assets | Vite + Tailwind CSS 3 + Alpine.js |
| Tests | PHPUnit (16 tests, 74 assertions) |

## Installation rapide (Laragon / XAMPP)

1. **Prérequis** : PHP 8.2+, Composer, Node 20+, MySQL (ou rien du tout avec SQLite).
2. Copier le template d'environnement :
   ```bash
   composer install
   cp env.example .env
   php artisan key:generate
   ```
   > Avec SQLite : décommentez les 3 lignes `DB_CONNECTION=sqlite` dans `.env`
   > et lancez `touch database/database.sqlite`.
3. Dépendances :
   ```bash
   composer install
   npm install
   ```
4. Base de données + données de démonstration :
   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```
5. Lancer les 4 processus (4 terminaux, ou le multi-terminal de Laragon) :
   ```bash
   php artisan serve              # http://localhost:8000
   php artisan reverb:start       # temps réel (ws://localhost:8080)
   npm run dev                    # assets Vite
   php artisan queue:work         # notifications en file d'attente
   ```

> **Windows / Laragon** : tout fonctionne aussi via `php artisan serve`.
> Avec XAMPP + Apache, pointer le vhost sur `public/` et garder les autres terminaux.

## Comptes de démonstration (mot de passe : `password`)

| Rôle | E-mail |
|---|---|
| 👤 Client | `client@pharmaconnect.cm` |
| 🏥 Pharmacie | `fondateur@pharmaconnect.cm` |
| 🛵 Livreur | `livreur@pharmaconnect.cm` |
| 👑 Admin | `admin@pharmaconnect.cm` |

Les comptes **pharmacie** et **livreur** créés via l'inscription restent
« en attente » jusqu'à leur validation par l'admin (`/admin/utilisateurs`).

## Tests

```bash
php artisan test        # ou vendor/bin/phpunit
```

Couverture : cycle complet d'une commande (panier → commande + paiement mock →
préparation → livraison → réception), décrément du stock, pages publiques et
espaces client/pharmacie/livreur/admin.

## Structure clé

```
app/
├── Contracts/          # PaymentGateway, SmsGateway, TelephonyGateway, ChatbotService
├── Services/           # Implémentations Mock* liées via AppServiceProvider
├── Enums/              # Statuts commande/livraison/paiement, rôles, opérateurs
├── Http/Controllers/   # Client/, Pharmacie/, Livreur/, Admin/, Auth/ (Breeze)
├── Http/Middleware/    # EnsureUserHasRole (role:), EnsureUserActif
├── Events/             # CommandeStatutChange, PositionLivreurMiseAJour, NouveauMessage…
├── Notifications/      # canaux database + broadcast
└── Policies/           # Avis, Commande, Livraison, Médicament, Message, Pharmacie

resources/js/           # echo.js (Reverb), leaflet.js, suivi.js, messagerie.js, graphiques.js
routes/web.php          # routes publiques, auth, client, pharmacie, livreur, admin
```

## Passer aux vraies passerelles

Les interfaces sont dans `app/Contracts/`, liées dans `AppServiceProvider`
en fonction de `PAYMENT_GATEWAY`, `SMS_GATEWAY`, `CHATBOT_SERVICE`.
Implémentez par exemple `App\Services\MtnMomoGateway` (payment), enregistrez-le
dans le `match()` du provider, et la bascule est transparente pour le reste du code.
