<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pharmacie_id')->constrained()->cascadeOnDelete();
            $table->foreignId('livreur_id')->nullable()->constrained()->nullOnDelete();
            $table->string('numero', 20)->unique(); // PC-2026-00001
            // en_attente | confirmee | refusee | prete | assignee | en_livraison | livree | annulee
            $table->enum('statut', [
                'en_attente', 'confirmee', 'refusee', 'prete',
                'assignee', 'en_livraison', 'livree', 'annulee',
            ])->default('en_attente')->index();
            $table->unsignedBigInteger('sous_total'); // FCFA
            $table->unsignedInteger('frais_livraison')->default(1000);
            $table->unsignedBigInteger('total'); // FCFA
            $table->string('adresse_livraison');
            $table->string('ville_livraison')->default('Douala');
            $table->decimal('latitude_livraison', 9, 6)->nullable();
            $table->decimal('longitude_livraison', 9, 6)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('confirmee_at')->nullable();
            $table->timestamp('prete_at')->nullable();
            $table->timestamp('assignee_at')->nullable();
            $table->timestamp('en_livraison_at')->nullable();
            $table->timestamp('livree_at')->nullable();
            $table->timestamp('annulee_at')->nullable();
            $table->timestamps();
        });

        Schema::create('commande_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medicament_id')->constrained()->restrictOnDelete();
            $table->foreignId('pharmacie_id')->constrained()->restrictOnDelete();
            $table->string('nom_medicament'); // copie figée au moment de la commande
            $table->unsignedInteger('prix_unitaire');
            $table->unsignedInteger('quantite');
            $table->unsignedBigInteger('sous_total');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commande_lignes');
        Schema::dropIfExists('commandes');
    }
};
