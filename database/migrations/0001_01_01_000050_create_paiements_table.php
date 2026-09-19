<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('reference', 40)->unique(); // référence passerelle
            // mtn_momo | orange_money
            $table->enum('operateur', ['mtn_momo', 'orange_money']);
            $table->unsignedBigInteger('montant'); // FCFA
            $table->string('devise', 3)->default('XAF');
            // initiate | initiate_invalide | en_attente_confirme | reussi | echoue
            $table->enum('statut', ['initie', 'initie_invalide', 'en_attente_confirmation', 'reussi', 'echoue'])
                ->default('initie')->index();
            $table->string('numero_payeur', 30)->nullable();
            $table->string('numero_paye', 30)->nullable();
            $table->text('reponse_brute')->nullable(); // JSON de la passerelle (mock)
            $table->timestamp('paye_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
