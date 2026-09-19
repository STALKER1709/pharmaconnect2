<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('commande_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pharmacie_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('medicament_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('livreur_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('note'); // 1 à 5
            $table->text('commentaire')->nullable();
            $table->timestamps();

            // Un avis par client et par cible (pharmacie ou médicament ou livreur)
            $table->unique(['client_id', 'pharmacie_id']);
            $table->unique(['client_id', 'medicament_id']);
            $table->unique(['client_id', 'livreur_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avis');
    }
};
