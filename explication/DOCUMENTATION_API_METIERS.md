# 🚀 Documentation API et Métiers Avancés - Module Sale

## 📋 Table des Matières

1. [API Mailing](#api-mailing)
2. [Métiers Avancés](#métiers-avancés)
3. [Architecture Modulaire](#architecture-modulaire)
4. [Services Métier](#services-métier)
5. [Workflows Automatiques](#workflows-automatiques)

---

## 📧 API Mailing

### Base URL

**`/api/sale/mailing`**

Tous les endpoints retournent des réponses JSON.

---

### 1. Liste des Endpoints

**GET** `/api/sale/mailing`

Retourne la documentation de tous les endpoints disponibles.

**Réponse :**
```json
{
  "endpoints": [
    {
      "method": "POST",
      "path": "/api/sale/mailing/test",
      "description": "Envoie un email de test",
      "body": {
        "to": "string (required)",
        "subject": "string (optional)",
        "message": "string (optional)"
      }
    }
  ]
}
```

**Où trouver le code :**
- **Contrôleur :** `src/Controller/Api/MailingApiController.php` (méthode `index()`)

---

### 2. Envoi d'Email de Test

**POST** `/api/sale/mailing/test`

**Body (JSON) :**
```json
{
  "to": "destinataire@example.com",
  "subject": "Test Email ArtEdu",
  "message": "Ceci est un email de test"
}
```

**Réponse :**
```json
{
  "success": true,
  "message": "Email envoyé avec succès à destinataire@example.com"
}
```

**Où trouver le code :**
- **Contrôleur :** `src/Controller/Api/MailingApiController.php` (méthode `testEmail()`)
- **Service :** `src/Service/Sale/SaleMailerService.php` (méthode `sendTestEmail()`)

---

### 3. Notification Offre Reçue

**POST** `/api/sale/mailing/offer-received`

**Body (JSON) :**
```json
{
  "sale_id": 1,
  "offer_id": 5
}
```

**Fonctionnalité :**
- Envoie un email au vendeur lorsqu'une offre est reçue

**Où trouver le code :**
- **Contrôleur :** `src/Controller/Api/MailingApiController.php` (méthode `offerReceived()`)
- **Service :** `src/Service/Sale/SaleMailerService.php` (méthode `sendOfferReceivedNotification()`)
- **Template :** `templates/emails/offer_received.html.twig`

---

### 4. Notification Offre Acceptée

**POST** `/api/sale/mailing/offer-accepted`

**Body (JSON) :**
```json
{
  "sale_id": 1,
  "offer_id": 5
}
```

**Fonctionnalité :**
- Envoie un email au client lorsque son offre est acceptée

**Où trouver le code :**
- **Contrôleur :** `src/Controller/Api/MailingApiController.php` (méthode `offerAccepted()`)
- **Service :** `src/Service/Sale/SaleMailerService.php` (méthode `sendOfferAcceptedNotification()`)
- **Template :** `templates/emails/offer_accepted.html.twig`

---

### 5. Notification Offre Refusée

**POST** `/api/sale/mailing/offer-refused`

**Body (JSON) :**
```json
{
  "sale_id": 1,
  "offer_id": 5
}
```

**Fonctionnalité :**
- Envoie un email au client lorsque son offre est refusée

**Où trouver le code :**
- **Contrôleur :** `src/Controller/Api/MailingApiController.php` (méthode `offerRefused()`)
- **Service :** `src/Service/Sale/SaleMailerService.php` (méthode `sendOfferRefusedNotification()`)
- **Template :** `templates/emails/offer_refused.html.twig`

---

### 6. Notification Paiement Confirmé

**POST** `/api/sale/mailing/payment-confirmed`

**Body (JSON) :**
```json
{
  "sale_id": 1,
  "offer_id": 5,
  "amount": 150.00
}
```

**Fonctionnalité :**
- Envoie un email de confirmation de paiement

**Où trouver le code :**
- **Contrôleur :** `src/Controller/Api/MailingApiController.php` (méthode `paymentConfirmed()`)
- **Service :** `src/Service/Sale/SaleMailerService.php` (méthode `sendPaymentConfirmedNotification()`)
- **Template :** `templates/emails/payment_confirmed.html.twig`

---

### 7. Notification Changement de Statut

**POST** `/api/sale/mailing/status-updated`

**Body (JSON) :**
```json
{
  "sale_id": 1,
  "recipient_email": "client@example.com",
  "recipient_name": "John Doe",
  "old_status": "en attente",
  "new_status": "vendue"
}
```

**Fonctionnalité :**
- Envoie un email lors du changement de statut d'une vente

**Où trouver le code :**
- **Contrôleur :** `src/Controller/Api/MailingApiController.php` (méthode `statusUpdated()`)
- **Service :** `src/Service/Sale/SaleMailerService.php` (méthode `sendStatusUpdatedNotification()`)
- **Template :** `templates/emails/status_updated.html.twig`

---

## 🏗️ Métiers Avancés

### 1. Système d'Offres Bidirectionnel

#### Concept

Le système permet une négociation bidirectionnelle :
- **Client** propose un montant (`offeredAmount`)
- **Vendeur** peut accepter ou proposer une contre-offre (`vendorAmount`)
- **Client** peut accepter la contre-offre
- Quand les deux acceptent → Offre mutuellement acceptée

#### Statuts des Offres

- **`pending`** : Offre initiale, en attente de réponse
- **`negotiating`** : En cours de négociation (contre-offre proposée)
- **`accepted`** : Mutuellement acceptée (client + vendeur)
- **`refused`** : Refusée par le vendeur

#### Logique Métier

```php
// src/Entity/Offer.php (méthode isMutuallyAccepted())
public function isMutuallyAccepted(): bool
{
    return $this->clientAccepted && $this->vendorAccepted;
}
```

**Où trouver le code :**
- **Entité :** `src/Entity/Offer.php`
- **Contrôleur :** `src/Controller/OfferController.php`
- **Repository :** `src/Repository/OfferRepository.php`

---

### 2. Workflow Automatique des Statuts

#### Statuts Disponibles

```php
// src/Enum/SaleStatus.php
enum SaleStatus: string {
    case EN_ATTENTE = 'en attente';
    case DISPONIBLE = 'disponible';
    case EN_PROGRESS = 'en progress';
    case VENDUE = 'vendue';
    case REFUSER = 'refuser';
    case PAYEMENT_EN_COURS = 'payement en cours';
    case PAYER = 'payer';
    case ANNULE = 'annule';
}
```

#### Workflow Automatique

```
EN_ATTENTE / DISPONIBLE
    ↓ (Offre acceptée)
EN_PROGRESS
    ↓ (Paiement confirmé)
VENDUE
    ↓ (Paiement traité)
PAYEMENT_EN_COURS
    ↓ (Paiement finalisé)
PAYER
```

**Où trouver le code :**
- **Enum :** `src/Enum/SaleStatus.php`
- **Entité :** `src/Entity/Sale.php` (méthode `updateStatusFromWorkflow()`)

---

### 3. Gestion d'Images Multiples

#### Concept

Chaque vente peut avoir :
- **1 image principale** (`Sale.image`)
- **Jusqu'à 5 images secondaires** (`SaleImage` collection)

#### Structure

```php
// src/Entity/SaleImage.php
- path: string              // Chemin de l'image
- sortOrder: int            // Ordre d'affichage
- isPrimary: bool           // Image principale
- sale: Sale                // Relation ManyToOne
```

#### Affichage

- **Image principale** : Affichée en grand (500px hauteur)
- **Images secondaires** : Affichées en miniatures (100px)
- **Toutes cliquables** : Agrandissement en modal

**Où trouver le code :**
- **Entité :** `src/Entity/SaleImage.php`
- **Service :** `src/Service/SaleImageUploader.php`
- **Entité Sale :** `src/Entity/Sale.php` (méthode `getSortedImages()`)

---

### 4. Génération de Factures PDF

#### Fonctionnalité

- ✅ Génération automatique après paiement
- ✅ Format PDF avec Dompdf
- ✅ Numéro de facture unique
- ✅ Détails complets (vente, client, montant)
- ✅ Téléchargement automatique
- ✅ Envoi par email en pièce jointe

#### Structure de la Facture

```
Numéro : INV-2024-000001-XXXXXX
Date : 16/12/2024
Client : Nom + Email
Œuvre : Titre de la vente
Montant : XXX,XX €
```

**Où trouver le code :**
- **Service :** `src/Service/InvoiceService.php`
- **Template :** `templates/invoices/invoice.html.twig`
- **Contrôleur :** `src/Controller/PaymentController.php` (méthode `confirmPayment()`)

---

### 5. Intégration Stripe Complète

#### Fonctionnalités

- ✅ Création de PaymentIntent
- ✅ Support achat direct et paiement via offre
- ✅ Métadonnées pour traçabilité
- ✅ Webhook pour mises à jour automatiques
- ✅ Gestion des erreurs

#### Flux Stripe

```
1. Frontend → Crée PaymentIntent
2. Stripe → Retourne clientSecret
3. Frontend → Confirme paiement avec Stripe.js
4. Stripe → Traite le paiement
5. Backend → Vérifie le statut
6. Backend → Génère facture + Envoie email
```

**Où trouver le code :**
- **Service :** `src/Service/PaymentService.php`
- **Contrôleur :** `src/Controller/PaymentController.php`
- **Template :** `templates/sale/payment.html.twig`

---

## 🏛️ Architecture Modulaire

### 1. Système d'Événements

#### Principe

Les contrôleurs **émettent des événements** au lieu d'appeler directement les services.

**Avantages :**
- ✅ Découplage complet
- ✅ Extensibilité (facile d'ajouter d'autres subscribers)
- ✅ Testabilité
- ✅ Pas de conflit entre modules

#### Événements Disponibles

| Événement | Déclenché par | Écouté par |
|-----------|---------------|------------|
| `PaymentConfirmedEvent` | `PaymentController::confirmPayment()` | `SaleEmailSubscriber` |
| `OfferReceivedEvent` | `OfferController::new()` | `SaleEmailSubscriber` |
| `OfferAcceptedEvent` | `OfferController::accept()` | `SaleEmailSubscriber` |
| `OfferRefusedEvent` | `OfferController::refuse()` | `SaleEmailSubscriber` |

**Où trouver le code :**
- **Événements :** `src/Event/Sale/*.php`
- **Subscriber :** `src/EventSubscriber/SaleEmailSubscriber.php`

---

### 2. Service Modulaire : SaleMailerService

#### Namespace Dédié

```php
namespace App\Service\Sale;
```

#### Interface Implémentée

```php
class SaleMailerService implements EmailServiceInterface
```

**Avantages :**
- ✅ Pas de conflit avec d'autres modules
- ✅ Configuration préfixée (`sale.mailer.*`)
- ✅ Réutilisable et extensible

**Où trouver le code :**
- **Service :** `src/Service/Sale/SaleMailerService.php`
- **Interface :** `src/Service/Interface/EmailServiceInterface.php`
- **Configuration :** `config/services.yaml`

---

### 3. Configuration Préfixée

#### Paramètres Modulaires

```yaml
# config/services.yaml
parameters:
    sale.mailer.from_email: 'hadjamorrached@gmail.com'
    sale.mailer.dsn: '%env(MAILER_DSN)%'
    sale.image_upload_directory: '%kernel.project_dir%/public/uploads/sales'
```

**Avantages :**
- ✅ Pas de conflit de paramètres
- ✅ Configuration claire et organisée
- ✅ Facile à surcharger

---

## 🔧 Services Métier

### 1. PaymentService

**Fichier :** `src/Service/PaymentService.php`

#### Méthodes

- `createPaymentIntent(Sale, Offer)` : Crée PaymentIntent pour offre
- `createDirectPurchaseIntent(Sale, amount, clientName, clientEmail)` : Crée PaymentIntent pour achat direct
- `getPaymentIntent(string $id)` : Récupère un PaymentIntent
- `confirmPayment(string $id)` : Confirme un paiement
- `cancelPaymentIntent(string $id)` : Annule un PaymentIntent

**Où trouver le code :**
- **Service :** `src/Service/PaymentService.php`

---

### 2. InvoiceService

**Fichier :** `src/Service/InvoiceService.php`

#### Méthodes

- `generateDirectPurchaseInvoice(Sale, clientName, clientEmail, amount, paymentIntentId)` : Génère facture PDF

**Fonctionnalités :**
- ✅ Génération PDF avec Dompdf
- ✅ Numéro de facture unique
- ✅ Template Twig personnalisable
- ✅ Sauvegarde dans `public/invoices/`

**Où trouver le code :**
- **Service :** `src/Service/InvoiceService.php`
- **Template :** `templates/invoices/invoice.html.twig`

---

### 3. SaleImageUploader

**Fichier :** `src/Service/SaleImageUploader.php`

#### Méthodes

- `upload(UploadedFile)` : Upload une image
- `uploadMultiple(array)` : Upload plusieurs images
- `delete(string $path)` : Supprime une image

**Fonctionnalités :**
- ✅ Validation des formats (JPEG, PNG, GIF, WebP)
- ✅ Validation de la taille (max 5MB)
- ✅ Génération de noms uniques
- ✅ Stockage sécurisé

**Où trouver le code :**
- **Service :** `src/Service/SaleImageUploader.php`

---

### 4. SaleMailerService

**Fichier :** `src/Service/Sale/SaleMailerService.php`

#### Méthodes

- `sendEmail(to, subject, template, context, attachments)` : Méthode générique
- `sendOfferReceivedNotification(Offer, Sale)` : Notification offre reçue
- `sendOfferAcceptedNotification(Offer, Sale)` : Notification offre acceptée
- `sendOfferRefusedNotification(Offer, Sale)` : Notification offre refusée
- `sendPaymentConfirmedNotification(Sale, email, name, amount)` : Notification paiement
- `sendDirectPurchaseConfirmation(Sale, name, email, amount, invoicePath)` : Confirmation achat direct
- `sendTestEmail(to, subject, message)` : Email de test

**Où trouver le code :**
- **Service :** `src/Service/Sale/SaleMailerService.php`
- **Templates :** `templates/emails/*.html.twig`

---

## 🔄 Workflows Automatiques

### 1. Workflow : Achat Direct

```
1. Client clique "Acheter" sur page d'accueil
   → Redirection vers /payment/sale/{id}?direct=1

2. Client remplit formulaire (nom, email, carte)
   → JavaScript crée PaymentIntent via /payment/sale/{id}/create-intent

3. Stripe traite le paiement
   → Frontend confirme via Stripe.js

4. Backend vérifie le paiement
   → PaymentController::confirmPayment()

5. Génération facture PDF
   → InvoiceService::generateDirectPurchaseInvoice()

6. Mise à jour statut
   → sale.status = VENDUE

7. Émission événement
   → PaymentConfirmedEvent

8. EventSubscriber envoie email
   → SaleEmailSubscriber::onPaymentConfirmed()
   → SaleMailerService::sendDirectPurchaseConfirmation()

9. Frontend télécharge facture
   → Téléchargement automatique du PDF

10. Page d'accueil
    → L'œuvre disparaît (filtrée par statut)
```

**Où trouver le code :**
- **Contrôleur :** `src/Controller/PaymentController.php`
- **Service :** `src/Service/PaymentService.php`, `src/Service/InvoiceService.php`
- **Événement :** `src/Event/Sale/PaymentConfirmedEvent.php`
- **Subscriber :** `src/EventSubscriber/SaleEmailSubscriber.php`

---

### 2. Workflow : Offre et Négociation

```
1. Client fait une offre
   → OfferController::new()
   → Crée Offer avec status='pending'

2. Émission événement
   → OfferReceivedEvent

3. EventSubscriber envoie email
   → SaleEmailSubscriber::onOfferReceived()
   → Email au vendeur

4. Vendeur voit l'offre
   → /sale/{id}/offers

5. Vendeur fait contre-offre
   → OfferController::negotiate()
   → offer.vendorAmount = montant
   → offer.status = 'negotiating'

6. Client accepte contre-offre
   → OfferController::accept() (côté client)
   → offer.clientAccepted = true

7. Vendeur accepte aussi
   → OfferController::accept() (côté vendeur)
   → offer.vendorAccepted = true
   → offer.status = 'accepted'
   → sale.status = VENDUE

8. Émission événement
   → OfferAcceptedEvent

9. EventSubscriber envoie email
   → SaleEmailSubscriber::onOfferAccepted()
   → Email au client

10. Client peut payer
    → /payment/sale/{id}
    → Paiement via offre acceptée
```

**Où trouver le code :**
- **Contrôleur :** `src/Controller/OfferController.php`
- **Événements :** `src/Event/Sale/Offer*Event.php`
- **Subscriber :** `src/EventSubscriber/SaleEmailSubscriber.php`

---

## 📊 Statistiques Techniques

### Routes API

- **Total :** 7 endpoints
- **Base URL :** `/api/sale/mailing`
- **Méthodes :** GET (1), POST (6)

### Services

- **Total :** 4 services métier
- **Services modulaires :** 1 (SaleMailerService)
- **Services génériques :** 3 (PaymentService, InvoiceService, SaleImageUploader)

### Événements

- **Total :** 4 événements
- **Subscribers :** 1 (SaleEmailSubscriber)
- **Méthodes subscriber :** 4

### Templates Email

- **Total :** 5 templates
- **Format :** HTML avec styles inline
- **Pièces jointes :** PDF facture (pour achat direct)

---

## 🔍 Points Techniques Avancés

### 1. Validation des Données

**Où trouver :**
- **Entités :** `src/Entity/*.php` (annotations `#[Assert\*]`)
- **Formulaires :** `src/Form/*.php` (contraintes de validation)

### 2. Sécurité

- ✅ Protection CSRF sur toutes les actions POST
- ✅ Validation côté serveur et client
- ✅ Upload sécurisé (validation type/taille)
- ✅ Injection de dépendances Symfony

**Où trouver :**
- **Formulaires :** `src/Form/*.php`
- **Contrôleurs :** `src/Controller/*.php` (méthode `isCsrfTokenValid()`)

### 3. Performance

- ✅ Lazy loading des relations Doctrine
- ✅ Requêtes optimisées dans les repositories
- ✅ Cache Symfony activé

**Où trouver :**
- **Repositories :** `src/Repository/*.php`
- **Entités :** `src/Entity/*.php` (relations Doctrine)

---

**Documentation complète des API et métiers avancés !** ✅

