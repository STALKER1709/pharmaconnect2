<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Profils 1–1 (héritage UML Utilisateur → Client/Pharmacie/Livreur)
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('adresse')->nullable();
            $table->string('ville')->default('Douala');
            $table->string('quartier')->nullable();
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->timestamps();
        });

        Schema::create('pharmacies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->string('adresse');
            $table->string('ville')->default('Douala');
            $table->string('quartier')->nullable();
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->string('photo')->nullable(); // stockée sur le disque public
            $table->string('document')->nullable(); // pièce justificative (validation admin)
            $table->enum('statut', ['en_attente', 'actif', 'suspendu'])->default('en_attente')->index();
            $table->unsignedInteger('frais_livraison')->default(1000); // FCFA
            $table->boolean('on_livraison')->default(false);
            $table->decimal('note_moyenne', 3, 2)->default(0);
            $table->unsignedInteger('nb_avis')->default(0);
            $table->timestamps();
        });

        Schema::create('livreurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('ville')->default('Douala');
            $table->enum('vehicule', ['moto', 'voiture', 'velo'])->default('moto');
            $table->string('immatriculation', 20)->nullable();
            $table->string('document')->nullable(); // permis / pièce d'identité
            $table->enum('statut', ['en_attente', 'actif', 'suspendu'])->default('en_attente')->index();
            $table->enum('disponibilite', ['disponible', 'en_course', 'hors_ligne'])->default('hors_ligne')->index();
            $table->decimal('note_moyenne', 3, 2)->default(0);
            $table->unsignedInteger('nb_avis')->default(0);
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->timestamp('derniere_position_at')->nullable();
            $table->timestamps();
        });

        // Horaires d'ouverture des pharmacies (0 = dimanche … 6 = samedi)
        Schema::create('horaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacie_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('jour');
            $table->boolean('ouvert')->default(true);
            $table->time('heure_ouverture')->default('08:00');
            $table->time('heure_fermeture')->default('20:00');
            $table->unique(['pharmacie_id', 'jour']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horaires');
        Schema::dropIfExists('livreurs');
        Schema::dropIfExists('pharmacies');
        Schema::dropIfExists('clients');
    }
};
