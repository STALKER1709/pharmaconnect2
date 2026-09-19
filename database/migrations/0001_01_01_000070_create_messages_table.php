<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('pharmacie_user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('livreur_user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('commande_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('dernier_message_at')->nullable();
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expediteur_id')->constrained('users')->cascadeOnDelete();
            $table->text('contenu');
            $table->timestamp('lu_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
    }
};
