# ❓ Questions Potentielles pour Validation - Module Sale

## 📋 Table des Matières

1. [Questions Générales sur le Projet](#questions-générales-sur-le-projet)
2. [Questions sur l'Architecture](#questions-sur-larchitecture)
3. [Questions sur les Fonctionnalités](#questions-sur-les-fonctionnalités)
4. [Questions Techniques](#questions-techniques)
5. [Questions sur l'Intégration](#questions-sur-lintégration)

---

## 🎯 Questions Générales sur le Projet

### Q1 : Quel est l'objectif de votre module Sale ?

**Réponse :**
Le module Sale est un système complet de gestion de ventes d'œuvres d'art permettant :
- La mise en vente d'œuvres (vente, ticket, échange)
- Un système d'offres bidirectionnel avec négociation
- L'intégration Stripe pour les paiements sécurisés
- La gestion d'images multiples par œuvre
- L'envoi automatique d'emails de notification
- La génération de factures PDF

**Où trouver :** `explication/DOCUMENTATION_FONCTIONNALITES.md` (section Vue d'Ensemble)

---

### Q2 : Quelles sont les technologies utilisées ?

**Réponse :**
- **Framework :** Symfony 6.x
- **ORM :** Doctrine
- **Templates :** Twig
- **Frontend :** Bootstrap 5, Swiper.js, JavaScript
- **Paiement :** Stripe API
- **Email :** Symfony Mailer + Gmail SMTP
- **PDF :** Dompdf
- **Architecture :** MVC + Event-Driven

**Où trouver :** `composer.json`, `package.json`

---

### Q3 : Combien de routes avez-vous implémentées ?

**Réponse :**
- **Routes publiques :** 2 (`/`, `/sale/{id}/details`)
- **Routes admin :** 8 (`/sale/*`)
- **Routes offre :** 6 (`/offer/*`)
- **Routes paiement :** 4 (`/payment/*`)
- **Routes API :** 7 (`/api/sale/mailing/*`)
- **Total :** 27 routes

**Où trouver :** `explication/DOCUMENTATION_FONCTIONNALITES.md` (section Statistiques)

---

## 🏗️ Questions sur l'Architecture

### Q4 : Expliquez votre architecture modulaire

**Réponse :**
J'ai implémenté une architecture modulaire pour éviter les conflits lors de l'intégration avec d'autres modules :

1. **Namespace dédié :** `App\Service\Sale\` pour le service de mailing
2. **Interface générique :** `EmailServiceInterface` pour découpler les services
3. **Système d'événements :** Utilisation d'EventDispatcher pour découpler les contrôleurs des services
4. **Configuration préfixée :** Paramètres avec préfixes (`sale.mailer.*`)
5. **Routes préfixées :** API avec préfixe unique (`/api/sale/mailing/*`)

**Avantages :**
- Pas de conflit de services
- Pas de conflit de routes
- Pas de conflit de configuration
- Extensibilité facile

**Où trouver :** `explication/ARCHITECTURE_MODULAIRE_SALE.md`

---

### Q5 : Comment fonctionne votre système d'événements ?

**Réponse :**
Le système utilise le pattern Event-Driven de Symfony :

1. **Contrôleurs émettent des événements** au lieu d'appeler directement les services
2. **EventSubscriber écoute les événements** et appelle les services appropriés
3. **4 événements créés :**
   - `PaymentConfirmedEvent` : Paiement confirmé
   - `OfferReceivedEvent` : Offre reçue
   - `OfferAcceptedEvent` : Offre acceptée
   - `OfferRefusedEvent` : Offre refusée

**Exemple :**
```php
// Contrôleur émet un événement
$event = new PaymentConfirmedEvent($sale, $email, $name, $amount, $invoicePath);
$eventDispatcher->dispatch($event, PaymentConfirmedEvent::NAME);

// Subscriber écoute et envoie l'email
public function onPaymentConfirmed(PaymentConfirmedEvent $event): void
{
    $this->mailerService->sendDirectPurchaseConfirmation(...);
}
```

**Où trouver :**
- **Événements :** `src/Event/Sale/*.php`
- **Subscriber :** `src/EventSubscriber/SaleEmailSubscriber.php`

---

### Q6 : Pourquoi avez-vous créé une interface EmailServiceInterface ?

**Réponse :**
Pour permettre à chaque module d'implémenter son propre service d'email sans conflit :

1. **Découplage :** Les contrôleurs dépendent de l'interface, pas de l'implémentation
2. **Extensibilité :** Facile d'ajouter d'autres modules (Notification, Newsletter, etc.)
3. **Testabilité :** Facile de mocker l'interface pour les tests
4. **Standards :** Respect du principe d'inversion de dépendance (SOLID)

**Où trouver :**
- **Interface :** `src/Service/Interface/EmailServiceInterface.php`
- **Implémentation :** `src/Service/Sale/SaleMailerService.php`

---

## ⚙️ Questions sur les Fonctionnalités

### Q7 : Expliquez le système d'offres bidirectionnel

**Réponse :**
Le système permet une négociation complète entre client et vendeur :

1. **Client propose** un montant (`offeredAmount`)
2. **Vendeur peut :**
   - Accepter directement
   - Proposer une contre-offre (`vendorAmount`)
   - Refuser
3. **Client peut accepter** la contre-offre
4. **Quand les deux acceptent** → Offre mutuellement acceptée → Statut VENDUE

**Statuts :**
- `pending` : En attente
- `negotiating` : En négociation
- `accepted` : Mutuellement acceptée
- `refused` : Refusée

**Où trouver :**
- **Entité :** `src/Entity/Offer.php`
- **Contrôleur :** `src/Controller/OfferController.php`
- **Méthode :** `Offer::isMutuallyAccepted()`

---

### Q8 : Comment fonctionne l'achat direct ?

**Réponse :**
L'achat direct permet d'acheter une œuvre sans passer par une offre :

1. **Page d'accueil** → Bouton "Acheter" → Redirection vers `/payment/sale/{id}?direct=1`
2. **Formulaire** → Client remplit nom, email, carte bancaire
3. **Stripe** → Traitement du paiement
4. **Backend** → Vérifie le paiement, génère facture PDF
5. **Événement** → `PaymentConfirmedEvent` émis
6. **Email** → Envoi automatique avec facture PDF en pièce jointe
7. **Téléchargement** → Facture téléchargée automatiquement
8. **Statut** → Sale.status = VENDUE
9. **Filtrage** → L'œuvre disparaît de la page d'accueil

**Où trouver :**
- **Contrôleur :** `src/Controller/PaymentController.php` (méthode `confirmPayment()`)
- **Service :** `src/Service/InvoiceService.php`
- **Template :** `templates/sale/payment.html.twig`

---

### Q9 : Comment gérez-vous les images multiples ?

**Réponse :**
Chaque vente peut avoir :
- **1 image principale** : Stockée dans `Sale.image`
- **Jusqu'à 5 images secondaires** : Stockées dans la collection `SaleImage`

**Fonctionnalités :**
- Upload sécurisé (validation format/taille)
- Image principale marquée (`isPrimary = true`)
- Tri par `sortOrder`
- Affichage : principale en grand, secondaires en miniatures
- Toutes cliquables et agrandissables en modal

**Où trouver :**
- **Entité :** `src/Entity/SaleImage.php`
- **Service :** `src/Service/SaleImageUploader.php`
- **Entité Sale :** `src/Entity/Sale.php` (méthode `getSortedImages()`)

---

### Q10 : Comment fonctionne la génération de factures PDF ?

**Réponse :**
La facture est générée automatiquement après un paiement réussi :

1. **Service InvoiceService** génère un PDF avec Dompdf
2. **Template Twig** : `templates/invoices/invoice.html.twig`
3. **Numéro unique** : Format `INV-YYYY-XXXXXX-XXXXXX`
4. **Sauvegarde** : `public/invoices/invoice_*.pdf`
5. **Envoi email** : Facture en pièce jointe
6. **Téléchargement** : Automatique côté frontend

**Où trouver :**
- **Service :** `src/Service/InvoiceService.php`
- **Template :** `templates/invoices/invoice.html.twig`
- **Contrôleur :** `src/Controller/PaymentController.php` (méthode `confirmPayment()`)

---

## 🔧 Questions Techniques

### Q11 : Comment avez-vous implémenté l'intégration Stripe ?

**Réponse :**
Intégration complète avec Stripe :

1. **Service PaymentService** : Encapsule toutes les interactions Stripe
2. **PaymentIntent** : Créé pour chaque paiement (direct ou via offre)
3. **Métadonnées** : Stockées dans PaymentIntent pour traçabilité
4. **Frontend** : Utilise Stripe.js pour le formulaire de paiement
5. **Backend** : Vérifie le statut après confirmation
6. **Webhook** : Reçoit les événements Stripe (optionnel)

**Méthodes principales :**
- `createPaymentIntent()` : Pour paiement via offre
- `createDirectPurchaseIntent()` : Pour achat direct
- `getPaymentIntent()` : Récupère un PaymentIntent
- `confirmPayment()` : Vérifie le statut

**Où trouver :**
- **Service :** `src/Service/PaymentService.php`
- **Contrôleur :** `src/Controller/PaymentController.php`
- **Template :** `templates/sale/payment.html.twig`

---

### Q12 : Comment gérez-vous les emails automatiques ?

**Réponse :**
Système d'emails automatiques via événements :

1. **Contrôleurs émettent des événements** (pas d'appel direct au service)
2. **SaleEmailSubscriber écoute** tous les événements Sale
3. **SaleMailerService envoie** les emails appropriés
4. **Templates Twig** : Un template par type d'email

**Types d'emails :**
- Notification offre reçue (au vendeur)
- Notification offre acceptée (au client)
- Notification offre refusée (au client)
- Confirmation paiement (avec facture PDF)
- Confirmation achat direct (avec facture PDF)

**Où trouver :**
- **Service :** `src/Service/Sale/SaleMailerService.php`
- **Subscriber :** `src/EventSubscriber/SaleEmailSubscriber.php`
- **Templates :** `templates/emails/*.html.twig`

---

### Q13 : Comment avez-vous sécurisé les uploads d'images ?

**Réponse :**
Sécurité multi-niveaux :

1. **Validation format** : Uniquement JPEG, PNG, GIF, WebP
2. **Validation taille** : Maximum 5MB par image
3. **Noms uniques** : Génération de noms aléatoires
4. **Stockage sécurisé** : Dossier `public/uploads/sales/`
5. **Validation côté serveur** : Contraintes Symfony dans le formulaire
6. **Validation côté client** : Attribut `accept` dans l'input file

**Où trouver :**
- **Service :** `src/Service/SaleImageUploader.php`
- **Form :** `src/Form/SaleType.php` (contraintes `#[Assert\File]`)

---

### Q14 : Comment fonctionne le filtrage des ventes sur la page d'accueil ?

**Réponse :**
Le filtrage exclut automatiquement les ventes vendues :

```php
// src/Controller/HomeSaleController.php
$sales = array_filter($allSales, function($sale) {
    $status = $sale->getStatus()->value;
    // Exclut : vendue, payer, annule, refuser, payement en cours
    return !in_array($status, ['vendue', 'payer', 'annule', 'refuser', 'payement en cours']);
});
```

**Résultat :**
- Seules les ventes disponibles s'affichent
- Les ventes vendues restent visibles dans l'admin
- Filtrage par type également disponible

**Où trouver :**
- **Contrôleur :** `src/Controller/HomeSaleController.php` (méthode `index()`)

---

## 🔗 Questions sur l'Intégration

### Q15 : Comment éviter les conflits avec d'autres modules ?

**Réponse :**
Architecture modulaire complète :

1. **Namespaces dédiés :** `App\Service\Sale\`
2. **Routes préfixées :** `/api/sale/mailing/*`
3. **Paramètres préfixés :** `sale.mailer.*`, `sale.image_upload_directory`
4. **Interface générique :** Chaque module implémente `EmailServiceInterface`
5. **Système d'événements :** Événements préfixés (`sale.payment.confirmed`)

**Exemple d'intégration :**
```yaml
# Module Sale
parameters:
    sale.mailer.from_email: 'sale@example.com'

# Module Notification (collaborateur)
parameters:
    notification.mailer.from_email: 'notif@example.com'
```

**Où trouver :** `explication/ARCHITECTURE_MODULAIRE_SALE.md`

---

### Q16 : Comment merger votre module avec celui d'un collaborateur ?

**Réponse :**
Processus de merge étape par étape :

1. **Créer une branche** : `feature/sale-module`
2. **Push votre code** : Tous les fichiers du module Sale
3. **Créer Pull Request** : Sur GitHub/GitLab
4. **Résoudre les conflits** : Principalement dans `config/services.yaml`
5. **Tester** : Vérifier que les deux modules fonctionnent

**Conflits courants :**
- `config/services.yaml` : Fusionner les configurations
- `composer.json` : Fusionner les dépendances
- Routes : Utiliser des préfixes différents

**Où trouver :** `explication/GUIDE_COLLABORATION_GIT.md`

---

### Q17 : Quelles sont les dépendances de votre module ?

**Réponse :**
**Dépendances PHP (composer.json) :**
- `symfony/framework-bundle` : Framework Symfony
- `doctrine/orm` : ORM Doctrine
- `twig/twig` : Moteur de templates
- `stripe/stripe-php` : API Stripe
- `dompdf/dompdf` : Génération PDF
- `symfony/mailer` : Envoi d'emails

**Dépendances JavaScript (optionnel) :**
- `swiper` : Carousel d'images
- `bootstrap` : Framework CSS
- `stripe.js` : Intégration Stripe frontend

**Où trouver :** `composer.json`, `package.json`

---

### Q18 : Comment tester votre module ?

**Réponse :**
**Tests manuels :**
1. Créer une vente avec images
2. Faire une offre
3. Accepter/refuser l'offre
4. Procéder au paiement
5. Vérifier l'email reçu
6. Vérifier la facture PDF

**Tests API :**
```bash
# Test email
curl -X POST http://localhost/api/sale/mailing/test \
  -H "Content-Type: application/json" \
  -d '{"to":"test@example.com","subject":"Test","message":"Test message"}'
```

**Où trouver :**
- **API :** `explication/API_MAILING.md`
- **Commandes :** `php bin/console app:test-email`

---

## 📝 Questions sur la Documentation

### Q19 : Où se trouve la documentation complète ?

**Réponse :**
Toute la documentation est dans le dossier `explication/` :

1. **GUIDE_COLLABORATION_GIT.md** : Comment push et merger
2. **DOCUMENTATION_FONCTIONNALITES.md** : Toutes les fonctionnalités détaillées
3. **DOCUMENTATION_API_METIERS.md** : API et métiers avancés
4. **ARCHITECTURE_MODULAIRE_SALE.md** : Architecture modulaire
5. **GUIDE_INSTALLATION.md** : Installation et configuration
6. **API_MAILING.md** : Documentation API Mailing

**Où trouver :** Dossier `explication/`

---

### Q20 : Comment un nouveau développeur peut-il comprendre votre code ?

**Réponse :**
**Étapes recommandées :**

1. **Lire la documentation** : `explication/DOCUMENTATION_FONCTIONNALITES.md`
2. **Comprendre l'architecture** : `explication/ARCHITECTURE_MODULAIRE_SALE.md`
3. **Explorer les routes** : Voir `src/Controller/*.php`
4. **Comprendre les entités** : Voir `src/Entity/*.php`
5. **Suivre un flux** : Par exemple, flux d'achat direct

**Structure claire :**
- Contrôleurs : `src/Controller/`
- Entités : `src/Entity/`
- Services : `src/Service/`
- Templates : `templates/`
- Événements : `src/Event/`

**Où trouver :** Toute la documentation dans `explication/`

---

## 🎓 Questions pour Démonstration

### Q21 : Pouvez-vous me montrer le flux complet d'un achat direct ?

**Réponse :**
1. **Page d'accueil** (`/`) → Affiche les ventes disponibles
2. **Clic "Acheter"** → Redirection vers `/payment/sale/{id}?direct=1`
3. **Formulaire** → Client remplit nom, email, carte bancaire
4. **Stripe.js** → Traite le paiement côté frontend
5. **Backend** → Vérifie le paiement, génère facture
6. **Événement** → `PaymentConfirmedEvent` émis
7. **Email** → Envoi automatique avec facture
8. **Téléchargement** → Facture PDF téléchargée
9. **Statut** → Vente marquée VENDUE
10. **Page d'accueil** → L'œuvre n'apparaît plus

**Où trouver :**
- **Contrôleur :** `src/Controller/PaymentController.php`
- **Template :** `templates/sale/payment.html.twig`
- **Service :** `src/Service/PaymentService.php`, `src/Service/InvoiceService.php`

---

### Q22 : Comment fonctionne le système de négociation ?

**Réponse :**
**Exemple de négociation :**

1. **Client propose** : 100€ (`offeredAmount = 100`)
2. **Vendeur contre-propose** : 150€ (`vendorAmount = 150`)
3. **Statut** : `negotiating`
4. **Client accepte** : `clientAccepted = true`
5. **Vendeur accepte** : `vendorAccepted = true`
6. **Statut** : `accepted` → Offre mutuellement acceptée
7. **Vente** : `sale.status = VENDUE`
8. **Client peut payer** : Montant final = 150€

**Où trouver :**
- **Contrôleur :** `src/Controller/OfferController.php`
- **Entité :** `src/Entity/Offer.php` (méthode `isMutuallyAccepted()`)

---

### Q23 : Quels sont les statuts possibles d'une vente ?

**Réponse :**
**Enum SaleStatus :**
- `EN_ATTENTE` : En attente de traitement
- `DISPONIBLE` : Disponible à la vente
- `EN_PROGRESS` : En cours de négociation
- `VENDUE` : Vente confirmée (après paiement)
- `REFUSER` : Vente refusée
- `PAYEMENT_EN_COURS` : Paiement en cours de traitement
- `PAYER` : Paiement finalisé
- `ANNULE` : Vente annulée

**Workflow :**
```
EN_ATTENTE → EN_PROGRESS → VENDUE → PAYEMENT_EN_COURS → PAYER
```

**Où trouver :**
- **Enum :** `src/Enum/SaleStatus.php`
- **Entité :** `src/Entity/Sale.php`

---

## 🔐 Questions sur la Sécurité

### Q24 : Comment avez-vous sécurisé les paiements ?

**Réponse :**
Sécurité multi-niveaux :

1. **Stripe** : Traitement sécurisé côté Stripe (PCI-DSS compliant)
2. **Validation backend** : Vérification du statut du paiement
3. **Métadonnées** : Traçabilité complète dans PaymentIntent
4. **CSRF** : Protection sur tous les formulaires
5. **Validation** : Vérification des montants et données

**Où trouver :**
- **Service :** `src/Service/PaymentService.php`
- **Contrôleur :** `src/Controller/PaymentController.php`

---

### Q25 : Comment gérez-vous les erreurs d'envoi d'email ?

**Réponse :**
Gestion d'erreurs robuste :

1. **Try-catch** : Toutes les méthodes d'envoi sont dans des blocs try-catch
2. **Logging** : Erreurs loggées avec `error_log()`
3. **Non-bloquant** : Les erreurs d'email ne bloquent pas le processus
4. **Validation email** : Vérification du format avant envoi

**Exemple :**
```php
try {
    $this->mailer->send($email);
} catch (\Exception $e) {
    error_log('Erreur envoi email: ' . $e->getMessage());
    // Ne bloque pas le processus
}
```

**Où trouver :**
- **Service :** `src/Service/Sale/SaleMailerService.php`

---

## 📊 Questions sur les Performances

### Q26 : Comment optimisez-vous les requêtes Doctrine ?

**Réponse :**
Optimisations appliquées :

1. **Lazy loading** : Relations chargées à la demande
2. **Requêtes ciblées** : Méthodes spécifiques dans les repositories
3. **Tri en base** : Utilisation de SQL pour trier les ventes
4. **Filtrage efficace** : Filtrage PHP pour les statuts

**Exemple :**
```php
// src/Repository/SaleRepository.php
public function findAllOrderedByStatus(): array
{
    // Requête SQL optimisée
    $sql = "SELECT id FROM sale ORDER BY created_at DESC";
    // Chargement sélectif
}
```

**Où trouver :**
- **Repository :** `src/Repository/SaleRepository.php`

---

## ✅ Checklist de Validation

Avant la présentation, vérifiez :

- [ ] Toutes les routes fonctionnent
- [ ] Les emails sont envoyés correctement
- [ ] Les paiements Stripe fonctionnent
- [ ] Les factures PDF sont générées
- [ ] Les images s'affichent correctement
- [ ] Le système d'offres fonctionne
- [ ] La documentation est à jour
- [ ] Le code est commenté
- [ ] Les erreurs sont gérées

---

**Toutes les questions potentielles sont documentées avec leurs réponses et l'emplacement du code !** ✅

