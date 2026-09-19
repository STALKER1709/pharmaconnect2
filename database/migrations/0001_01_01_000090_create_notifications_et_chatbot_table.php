<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Conversations avec le chatbot (conseils santé)
        Schema::create('chatbot_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('role')->nullable(); // user | assistant
            $table->text('message');
            $table->timestamps();
        });

        // Pièces jointes génériques (ordonnances, photos de réception…)
        Schema::create('pieces_jointes', function (Blueprint $table) {
            $table->id();
            $table->morphs('piece_jointable');
            $table->string('chemin'); // disque public
            $table->string('nom_original')->nullable();
            $table->string('type', 30)->nullable(); // ordonnance | photo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pieces_jointes');
        Schema::dropIfExists('chatbot_messages');
        Schema::dropIfExists('notifications');
    }
};
