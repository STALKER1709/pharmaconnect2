<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livraisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('livreur_id')->nullable()->constrained()->nullOnDelete();
            // disponible | assignee | acceptee | en_route | arrivee | livree | echec
            $table->enum('statut', ['disponible', 'assignee', 'acceptee', 'en_route', 'arrivee', 'livree', 'echec'])
                ->default('disponible')->index();
            $table->decimal('latitude_depart', 9, 6)->nullable();
            $table->decimal('longitude_depart', 9, 6)->nullable();
            $table->decimal('latitude_arrivee', 9, 6)->nullable();
            $table->decimal('longitude_arrivee', 9, 6)->nullable();
            $table->unsignedInteger('distance_km')->nullable(); // arrondie
            $table->unsignedInteger('duree_estimee_min')->nullable();
            $table->timestamp('acceptee_at')->nullable();
            $table->timestamp('en_route_at')->nullable();
            $table->timestamp('arrivee_at')->nullable();
            $table->timestamp('livree_at')->nullable();
            $table->timestamps();
        });

        // Historique des positions du livreur (temps réel + trajectoire)
        Schema::create('position_livreurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livreur_id')->constrained()->cascadeOnDelete();
            $table->foreignId('livraison_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 9, 6);
            $table->decimal('longitude', 9, 6);
            $table->timestamp('signalee_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('position_livreurs');
        Schema::dropIfExists('livraisons');
    }
};
