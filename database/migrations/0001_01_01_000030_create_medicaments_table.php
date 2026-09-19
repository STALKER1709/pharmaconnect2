<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicaments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->string('reference', 50)->nullable(); // référence fabricant
            $table->text('description')->nullable();
            $table->text('posologie')->nullable();
            $table->boolean('ordonnance_obligatoire')->default(false);
            $table->string('photo')->nullable(); // disque public
            $table->string('fabricant')->nullable();
            $table->string('forme', 50)->nullable(); // comprimé, sirop, gélule…
            $table->unsignedInteger('dosage_mg')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        // Catalogue par pharmacie : stock, prix (FCFA), péremption
        Schema::create('pharmacie_medicament', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacie_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medicament_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantite')->default(0);
            $table->unsignedInteger('prix'); // FCFA
            $table->date('date_peremption')->nullable();
            $table->unsignedTinyInteger('seuil_stock_bas')->default(5);
            $table->timestamps();

            $table->unique(['pharmacie_id', 'medicament_id']);
            $table->index('date_peremption');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacie_medicament');
        Schema::dropIfExists('medicaments');
    }
};
