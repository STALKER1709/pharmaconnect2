<?php

namespace App\Services\Mock;

use App\Contracts\ChatbotService;

/**
 * Chatbot mock : répond à une FAQ par mots-clés.
 * Affiche systématiquement un avertissement : il ne remplace pas un
 * pharmacien ou un médecin, ne pose aucun diagnostic et ne donne aucune
 * posologie. Structure prête pour brancher un LLM via .env.
 */
class MockChatbotService implements ChatbotService
{
    private const AVERTISSEMENT = "\n\n⚠️ **Avertissement important** : je suis un assistant informatif et je ne remplace ni un pharmacien, ni un médecin. Je ne pose aucun diagnostic et ne donne aucune posologie. En cas de symptômes graves (difficulté à respirer, douleur intense, fièvre élevée persistante, perte de connaissance), contactez immédiatement les urgences ou rendez-vous dans un centre de santé.";

    /** @var array<string, string> FAQ par mots-clés */
    private const FAQ = [
        'paludisme|palu|moustique' => "Le paludisme est fréquent au Cameroun. Les moyens de prévention incluent les moustiquaires imprégnées et l'élimination des eaux stagnantes. En cas de fièvre, faites un test de dépistage rapide en pharmacie ou dans un centre de santé. Vous trouverez des tests et traitements dans les pharmacies partenaires de PharmaConnect.",
        'fièvre|fievre|temperature' => "La fièvre peut avoir de nombreuses causes. Mesurez votre température, hydratez-vous et consultez un professionnel si elle dépasse 38,5 °C ou persiste plus de 48 h. Un test paludisme en pharmacie est recommandé au Cameroun.",
        'douleur|mal de tête|migraine|cephalee' => "Pour un mal de tête occasionnel, le repos et l'hydratation aident. Si les douleurs sont fréquentes, intenses ou accompagnées d'autres symptômes, consultez un médecin. Recherchez « antalgique » dans notre moteur pour voir les produits disponibles en pharmacie.",
        'toux|rhume|grippe|gorge' => "En cas de toux ou de rhume : hydratation, repos, miel (si non contre-indiqué). Si la toux dure plus de 2 semaines, consultez un professionnel de santé. Des sirops et pastilles sont disponibles dans les pharmacies partenaires.",
        'diarrhee|diarrhée|vomissement|deshydratation' => "Priorité : réhydratation (SRO disponibles en pharmacie) et hygiène stricte. Consultez rapidement si du sang apparaît, si les symptômes durent plus de 48 h ou concernant un jeune enfant.",
        'ordonnance|prescription' => "Pour les médicaments sur ordonnance : ajoutez le produit à votre panier puis téléversez une photo de votre ordonnance au moment de la commande. La pharmacie la validera avant de préparer votre commande.",
        'paiement|momo|orange money|mtn' => "PharmaConnect accepte MTN Mobile Money et Orange Money. Après votre commande, choisissez votre opérateur, renseignez votre numéro et validez. En mode démonstration locale, une page de simulation vous permet de tester le flux complet.",
        'livraison|livreur|delai|delai' => "Après acceptation par la pharmacie, un livreur proche est assigné. Vous pouvez suivre sa position en temps réel sur la carte jusqu'à la confirmation de réception.",
        'panier|commande' => "Ajoutez des médicaments depuis la recherche, vérifiez votre panier, puis validez votre commande. Si votre panier couvre plusieurs pharmacies, il sera automatiquement découpé en une commande par pharmacie.",
        'avis|note|evaluation' => "Après une livraison confirmée, vous pouvez noter le service de la pharmacie et les médicaments reçus (note de 1 à 5 + commentaire).",
    ];

    public function repondre(string $question, array $historique = []): array
    {
        $question = mb_strtolower(trim($question));
        $reponse = null;

        foreach (self::FAQ as $motsCles => $reponseFaq) {
            foreach (explode('|', $motsCles) as $motCle) {
                if ($motCle !== '' && str_contains($question, $motCle)) {
                    $reponse = $reponseFaq;
                    break 2;
                }
            }
        }

        if ($reponse === null) {
            $reponse = "Je n'ai pas trouvé de réponse précise à votre question. Voici les sujets que je connais : paludisme, fièvre, douleurs, toux/rhume, diarrhée, ordonnances, paiement Mobile Money, livraison, panier et avis. Reformulez si besoin, ou adressez-vous à une pharmacie partenaire via la messagerie.";
        }

        return [
            'reponse' => $reponse.self::AVERTISSEMENT,
            'source' => 'mock',
        ];
    }
}
