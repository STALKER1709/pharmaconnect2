<?php

namespace App\Contracts;

/**
 * Service de chatbot pour les conseils de santé.
 * Implémentation mock par défaut : MockChatbotService (base de règles locale).
 */
interface ChatbotService
{
    /**
     * Répond à une question de conseil.
     *
     * @param  string  $question  Question de l'utilisateur
     * @param  array<int, array{role: string, message: string}>  $historique  Historique de la conversation
     * @return string Réponse du chatbot
     */
    public function conseiller(string $question, array $historique = []): string;
}
