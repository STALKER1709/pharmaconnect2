# Prompts Google Stitch — PharmaConnect

Prompts prêts à coller sur [stitch.withgoogle.com](https://stitch.withgoogle.com) (mode **Desktop**).
Collez le bloc design system + le prompt de l'écran dans le même prompt. Export attendu : **HTML/Tailwind**.

> Les prompts sont en anglais (meilleurs résultats Stitch) mais toute la copy UI est en français,
> prix en FCFA — les écrans sortent directement exploitables dans l'app.

---

## Bloc design system (à coller au début de CHAQUE prompt)

```text
CONTEXT — PharmaConnect: a medicine-ordering web platform for Cameroon (clients order from local pharmacies, pay with Mobile Money, couriers deliver). Desktop web app, all UI copy in FRENCH, prices in FCFA (e.g. "12 500 FCFA").
DESIGN SYSTEM — Primary green #16a34a (pharmacy mint green, full scale #f0fdf6 → #14532d); page background #f0fdf6; text slate-800; white cards with 16px radius and soft shadow; buttons 12px radius, primary = solid #16a34a, secondary = white with light green border; pill-shaped badges; clean sans-serif; generous whitespace; trustworthy healthcare aesthetic. No gradients, no purple.
```

---

## 1. Accueil (visiteur)

```text
[PASTE DESIGN SYSTEM] —
Design the public landing page: sticky navbar (logo "PharmaConnect", links Médicaments, Pharmacies, boutons Connexion / Créer un compte), hero with headline "Vos médicaments, livrés à Douala", search bar "Rechercher un médicament…" + green CTA, trust badges (Pharmacies agréées · Paiement Mobile Money · Livraison suivie), a grid of 8 medication cards (photo, name, category pill, price "3 500 FCFA", availability badge "En stock" green / "Rupture" red, button "Ajouter au panier"), a section "6 pharmacies partenaires" with pharmacy cards (logo, name, quartier, open/closed badge, rating stars), footer with links. Mobile Money logos MTN MoMo + Orange Money in hero.
```

## 2. Recherche de médicaments

```text
[PASTE DESIGN SYSTEM] —
Design the medication search page: left sidebar with filters (Categories checkboxes with icons, Disponibilité toggle, Prix range slider in FCFA, Distance slider km, "Pharmacie de garde" checkbox), top bar with search input "Paracetamol…" and sort dropdown (Prix croissant, Distance, Pertinence), results grid of medication cards: photo, name, dosage, category pill, price "2 000 FCFA", pharmacies count "Disponible dans 4 pharmacies", green button "Voir les pharmacies". Show result count "128 médicaments trouvés".
```

## 3. Fiche médicament + pharmacies en stock

```text
[PASTE DESIGN SYSTEM] —
Design the medication detail page: breadcrumb, two-column layout. Left: large product photo, name "Paracétamol 500 mg", description, warning box "Sur ordonnance" (yellow) if needed. Right: price "2 000 FCFA" big, quantity stepper, green button "Ajouter au panier". Below: section "Disponible dans 4 pharmacies" — table/cards of pharmacies with name, quartier, distance "1,2 km", price at this pharmacy, open badge, button "Commander". Section "Avis clients" with star ratings and French comments.
```

## 4. Fiche pharmacie

```text
[PASTE DESIGN SYSTEM] —
Design the pharmacy profile page: header card with pharmacy logo, name "Pharmacie du Centre", quartier Bonanjo, Douala, rating "4,6 ★ (128 avis)", badges "Ouverte" green with hours "Lun–Sam 8h–20h" or "Fermée" red, distance chip, button "💬 Contacter" and phone button "📞 Appeler". Two columns: left = opening hours table per day, list of "Médicaments phares" cards with FCFA prices; right = interactive map placeholder with pin + section "Avis" (stars, French comments, date).
```

## 5. Panier + Checkout Mobile Money

```text
[PASTE DESIGN SYSTEM] —
Design the checkout page (single screen, 2 columns). Left: order summary card "Votre panier — Pharmacie du Centre" with 3 line items (name, qty stepper, price, remove ✕), sous-total "7 000 FCFA", frais de livraison "1 000 FCFA", TOTAL "8 000 FCFA" bold. Below: delivery form (Adresse de livraison, Ville, button "📍 Utiliser ma position actuelle" with geolocation icon, Notes). Right: payment card "Paiement Mobile Money" — operator choice as two selectable cards MTN MoMo (yellow) / Orange Money (orange), input "Numéro Mobile Money (6XX XX XX XX)", big green button "Payer 8 000 FCFA", security note "Paiement sécurisé · Référence PAY-XXXX".
```

## 6. Suivi de livraison temps réel

```text
[PASTE DESIGN SYSTEM] —
Design the live delivery tracking page: top status timeline with 5 steps (Confirmée ✓, Prête ✓, Assignée ✓, En livraison ● active green pulse, Livrée ○). Main area: large map (OpenStreetMap style) showing pharmacy pin 🏥, courier pin 🛵 moving, destination pin 📍, route dashed line. Side panel: order number "CMD-2026-0042", courier card (avatar, name, rating, buttons "📞 Appeler" and "💬 Message"), destination address, big green button "✅ Confirmer la réception" (only when status = En livraison), small text "Position actualisée il y a 12 s".
```

## 7. Messagerie

```text
[PASTE DESIGN SYSTEM] —
Design the real-time chat page: left column = conversation list (interlocutor avatar, name, last message preview, unread badge green, time), right column = active conversation with header (avatar, name "Pharmacie du Centre", online dot), scrollable message thread with bubbles: my messages solid green right-aligned, other messages white with green ring left-aligned, timestamps, date separators "Aujourd'hui". Bottom: message input + send button green. French sample messages about a medication order.
```

## 8. Dashboard pharmacie

```text
[PASTE DESIGN SYSTEM] —
Design the pharmacy dashboard: sidebar (logo, Dashboard, Médicaments, Stocks, Commandes, Paiements, Messagerie, Horaires, Statistiques) with active item highlighted green. Top row of 4 stat cards: "CA du mois 1 245 000 FCFA" with mini trend, "Commandes 86", "En attente 5" amber, "Stocks bas 3" red. Main: area chart "Chiffre d'affaires" (FCFA), bar chart "Commandes par jour", table "Top médicaments" (name, vendus, revenus FCFA). Right column: list "Commandes à traiter" with buttons Accepter (green) / Refuser (red) and badge statut.
```

## 9. Dashboard livreur

```text
[PASTE DESIGN SYSTEM] —
Design the courier dashboard: header with availability toggle "En ligne / Hors ligne" (green switch), earnings card "Gains du jour 15 000 FCFA". Section "Livraisons disponibles" — cards with 🏥 pickup pharmacy name + quartier, 📍 drop-off address, distance "3,2 km", fee "1 500 FCFA", green button "Accepter". Section "Ma livraison en cours" with status stepper, big map placeholder, action buttons: "🛵 Démarrer la course", "📍 Arrivé sur place", "✅ Marquer livrée" (primary green, shown progressively). Position sharing indicator "Partage de position actif" with pulsing dot.
```

## 10. Admin — validation des comptes

```text
[PASTE DESIGN SYSTEM] —
Design the admin account validation page: sidebar admin (Utilisateurs, Catégories, Statistiques). Top stat chips: "Pharmacies en attente 4" amber, "Livreurs en attente 2" amber, "Comptes actifs 312". Main table of pending accounts: type badge (Pharmacie green / Livreur blue), name, email, phone "690 12 34 56", request date, action buttons "✅ Valider" green and "✖ Refuser" red outline. Below: table of active users with status pills (Actif green, Suspendu red) and suspend/reactivate actions.
```

## 11. Chatbot conseil

```text
[PASTE DESIGN SYSTEM] —
Design the health chatbot page: centered chat panel with bot avatar "PharmaBot — Conseils santé", welcome message in French, suggestion chips ("J'ai de la fièvre", "Médicament pour la toux", "Posologie paracétamol"), user/bot message bubbles (same style as messaging), disclaimer banner at bottom "⚠️ PharmaBot ne remplace pas l'avis d'un professionnel de santé", input "Posez votre question…".
```

---

## Workflow après génération

1. Itérez dans Stitch avec des prompts courts (« move the payment card to the right », « use FCFA prices with thin space »).
2. **Export HTML/Tailwind** → déposez les fichiers dans un dossier `stitch/` du repo.
3. Demandez la conversion : les exports sont intégrés en vues Blade avec les classes
   `.card`, `.btn-primary`, `.badge`, `.input` existantes, les routes et les données réelles.
