<?php

namespace App\Services;

use App\Contracts\ChatbotService;

/**
 * Chatbot MOCK : moteur de règles local (aucune API externe).
 * Répond aux questions courantes de santé et oriente vers la pharmacie.
 */
class MockChatbotService implements ChatbotService
{
    /** @var array<string, array<int, string>> */
    private array $regles = [
        'paludisme|palu|fièvre|fievre|frisson' => [
            'La fièvre peut évoquer le paludisme, très fréquent au Cameroun. Je vous conseille de faire un TDR (test rapide) en pharmacie. Hydratez-vous bien et prenez du paracétamol pour faire baisser la fièvre. Si la fièvre dépasse 48 h ou dépasse 39 °C, consultez un médecin.',
        ],
        'migraine|mal de tête|mal de tete|céphalée|cephalee' => [
            'Pour les maux de tête : reposez-vous dans un endroit calme, hydratez-vous. Le paracétamol (500 mg à 1 g, max 3 g/jour) peut soulager. Évitez l\'automédication répétée : si les crises se multiplient, parlez-en à un pharmacien.',
        ],
        'toux|rhume|grippe|nez qui coule' => [
            'En cas de toux ou de rhume : buvez chaud, reposez-vous. Le miel et les infusions aident. Une toux persistante plus de 2 semaines nécessite un avis médical (possible tuberculose ou asthme).',
        ],
        'diarrhée|diarrhee|vomissement' => [
            'Priorité : réhydratation ! Utilisez des SRO (sachets de réhydratation orale, disponibles en pharmacie) et buvez beaucoup. Consultez si cela dure plus de 48 h, s\'il y a du sang, ou pour un jeune enfant.',
        ],
        'paracétamol|paracetamol|doliprane' => [
            'Le paracétamol se prend à 500 mg–1 g par prise, maximum 3 g par jour pour l\'adulte, espacé de 6 h. Ne dépassez jamais la dose : risque grave pour le foie.',
        ],
        'amoxicilline|antibiotique' => [
            'Les antibiotiques ne se prennent QUE sur ordonnance. Une cure inachevée ou mal choisie favorise les résistances. Décrivez vos symptômes à un médecin, puis trouvez votre traitement dans une pharmacie PharmaConnect.',
        ],
        'médicament|medicament|ordonnance|commander' => [
            'Pour commander : cherchez votre médicament dans la barre de recherche, vérifiez la disponibilité près de vous, ajoutez au panier, payez par MTN MoMo ou Orange Money, puis suivez votre livraison en temps réel. 🚚',
        ],
        'livraison|suivi|livreur' => [
            'Votre livraison est suivie en temps réel : la carte affiche la position du livreur. Vous recevez une notification à chaque étape. À la réception, confirmez-la depuis « Mes commandes ».',
        ],
        'paiement|momo|orange money|payer' => [
            'PharmaConnect accepte MTN MoMo et Orange Money (mode simulation en local). Le paiement est déclenché à la commande ; vous confirmez sur votre téléphone avec votre code secret.',
        ],
        'emergency|urgence|empoisonnement|accident' => [
            '⚠️ Il semble s\'agir d\'une urgence. Appelez immédiatement les secours : SAMU 1199 ou la police 117 au Cameroun, ou rendez-vous dans la structure de santé la plus proche.',
        ],
    ];

    private string $reponseDefaut = 'Je ne suis qu\'un assistant de démonstration (mode local). Je peux vous renseigner sur : la fièvre/palu, les maux de tête, la toux, la diarrhée, le paracétamol, les antibiotiques, les paiements Mobile Money, les livraisons, ou commander un médicament. Pour un avis médical personnalisé, consultez votre médecin ou un pharmacien.';

    public function conseiller(string $question, array $historique = []): string
    {
        $q = mb_strtolower(trim($question));

        foreach ($this->regles as $motifs => $reponses) {
            foreach (explode('|', $motifs) as $motif) {
                if ($motif !== '' && str_contains($q, $motif)) {
                    return $reponses[array_rand($reponses)];
                }
            }
        }

        return $this->reponseDefaut;
    }
}
