# ✅ Architecture Modulaire pour le Module Sale

## 🎯 Objectif

Mettre en place une architecture modulaire pour éviter les conflits lors de l'intégration avec d'autres modules, notamment les API de mailing.

---

## ✅ Solutions Appliquées

### 1. Interface EmailServiceInterface

**Fichier :** `src/Service/Interface/EmailServiceInterface.php`

- ✅ Interface générique pour tous les services d'envoi d'email
- ✅ Permet de découpler les services et d'éviter les conflits
- ✅ Chaque module peut implémenter sa propre version

```php
interface EmailServiceInterface
{
    public function sendEmail(
        string $to,
        string $subject,
        string $template,
        array $context = [],
        array $attachments = []
    ): void;
}
```

---

### 2. Service Modulaire : SaleMailerService

**Fichier :** `src/Service/Sale/SaleMailerService.php`

- ✅ Namespace dédié : `App\Service\Sale\`
- ✅ Implémente `EmailServiceInterface`
- ✅ Service spécifique au module Sale
- ✅ Pas de conflit avec d'autres modules

**Configuration :**
```yaml
# config/services.yaml
App\Service\Sale\SaleMailerService:
    arguments:
        $fromEmail: '%sale.mailer.from_email%'
```

---

### 3. Système d'Événements Symfony

**Événements créés :**

1. **PaymentConfirmedEvent** (`src/Event/Sale/PaymentConfirmedEvent.php`)
   - Déclenché lors de la confirmation d'un paiement
   - Contient : Sale, clientEmail, clientName, amount, invoicePath

2. **OfferReceivedEvent** (`src/Event/Sale/OfferReceivedEvent.php`)
   - Déclenché lorsqu'une offre est reçue
   - Contient : Offer, Sale

3. **OfferAcceptedEvent** (`src/Event/Sale/OfferAcceptedEvent.php`)
   - Déclenché lorsqu'une offre est acceptée
   - Contient : Offer, Sale

4. **OfferRefusedEvent** (`src/Event/Sale/OfferRefusedEvent.php`)
   - Déclenché lorsqu'une offre est refusée
   - Contient : Offer, Sale

**Avantages :**
- ✅ Découplage complet : les contrôleurs n'appellent plus directement le service
- ✅ Extensibilité : facile d'ajouter d'autres subscribers
- ✅ Testabilité : facile de mocker les événements

---

### 4. EventSubscriber : SaleEmailSubscriber

**Fichier :** `src/EventSubscriber/SaleEmailSubscriber.php`

- ✅ Écoute tous les événements liés au module Sale
- ✅ Gère l'envoi des emails de manière centralisée
- ✅ Automatiquement enregistré par Symfony (autoconfigure)

**Événements écoutés :**
- `sale.payment.confirmed` → Envoie email de confirmation de paiement
- `sale.offer.received` → Envoie notification au vendeur
- `sale.offer.accepted` → Envoie notification au client
- `sale.offer.refused` → Envoie notification au client

---

### 5. Configuration Modulaire

**Fichier :** `config/services.yaml`

**Paramètres préfixés :**
```yaml
parameters:
    # Module Sale - Configuration
    sale.image_upload_directory: '%kernel.project_dir%/public/uploads/sales'
    sale.mailer.from_email: 'hadjamorrached@gmail.com'
    sale.mailer.dsn: '%env(MAILER_DSN)%'
    
    # Module Payment - Configuration
    payment.stripe.secret_key: '%env(STRIPE_SECRET_KEY)%'
    payment.stripe.public_key: '%env(STRIPE_PUBLIC_KEY)%'
```

**Avantages :**
- ✅ Pas de conflit de paramètres entre modules
- ✅ Configuration claire et organisée
- ✅ Facile à surcharger via `.env`

---

### 6. Routes API Préfixées

**Fichier :** `src/Controller/Api/MailingApiController.php`

**Route :** `/api/sale/mailing/*`

- ✅ Préfixe unique : `/api/sale/mailing`
- ✅ Pas de conflit avec d'autres modules
- ✅ Structure claire et organisée

**Exemples de routes :**
- `POST /api/sale/mailing/test` - Test d'envoi d'email
- `POST /api/sale/mailing/offer-received` - Notification offre reçue
- `POST /api/sale/mailing/offer-accepted` - Notification offre acceptée
- etc.

---

## 📋 Architecture Finale

```
src/
├── Service/
│   ├── Interface/
│   │   └── EmailServiceInterface.php          # Interface générique
│   └── Sale/
│       └── SaleMailerService.php              # Service spécifique Sale
├── Event/
│   └── Sale/
│       ├── PaymentConfirmedEvent.php          # Événement paiement
│       ├── OfferReceivedEvent.php             # Événement offre reçue
│       ├── OfferAcceptedEvent.php             # Événement offre acceptée
│       └── OfferRefusedEvent.php              # Événement offre refusée
├── EventSubscriber/
│   └── SaleEmailSubscriber.php                # Gestionnaire d'emails
└── Controller/
    ├── PaymentController.php                  # Utilise les événements
    ├── OfferController.php                    # Utilise les événements
    └── Api/
        └── MailingApiController.php            # Routes préfixées
```

---

## 🔄 Flux d'Exécution

### Exemple : Confirmation de Paiement

1. **PaymentController** → `confirmPayment()`
   - Traite le paiement
   - Met à jour le statut de la vente
   - **Émet un événement** : `PaymentConfirmedEvent`

2. **EventDispatcher** → Dispatch l'événement
   - Cherche tous les subscribers qui écoutent cet événement

3. **SaleEmailSubscriber** → `onPaymentConfirmed()`
   - Reçoit l'événement
   - Appelle `SaleMailerService->sendDirectPurchaseConfirmation()`

4. **SaleMailerService** → Envoie l'email
   - Utilise `MailerInterface` de Symfony
   - Génère le template Twig
   - Envoie l'email avec la facture PDF

---

## ✅ Avantages de cette Architecture

### 1. **Pas de Conflits**
- ✅ Namespace dédié : `App\Service\Sale\`
- ✅ Paramètres préfixés : `sale.mailer.*`
- ✅ Routes préfixées : `/api/sale/mailing/*`

### 2. **Découplage**
- ✅ Contrôleurs → Événements (pas de dépendance directe au service)
- ✅ EventSubscriber → Service (gestion centralisée)
- ✅ Service → MailerInterface (abstraction Symfony)

### 3. **Extensibilité**
- ✅ Facile d'ajouter de nouveaux événements
- ✅ Facile d'ajouter de nouveaux subscribers
- ✅ Facile d'ajouter de nouveaux modules

### 4. **Testabilité**
- ✅ Facile de mocker les événements
- ✅ Facile de tester les subscribers indépendamment
- ✅ Facile de tester les services isolément

### 5. **Maintenabilité**
- ✅ Code organisé par module
- ✅ Responsabilités claires
- ✅ Documentation intégrée

---

## 🔧 Intégration avec d'Autres Modules

### Exemple : Module Notification

Pour ajouter un module Notification sans conflit :

1. **Créer le service :**
```php
// src/Service/Notification/NotificationMailerService.php
namespace App\Service\Notification;

class NotificationMailerService implements EmailServiceInterface
{
    // ...
}
```

2. **Configurer :**
```yaml
# config/services.yaml
parameters:
    notification.mailer.from_email: '%env(NOTIFICATION_MAILER_FROM_EMAIL)%'

services:
    App\Service\Notification\NotificationMailerService:
        arguments:
            $fromEmail: '%notification.mailer.from_email%'
```

3. **Créer les événements :**
```php
// src/Event/Notification/NotificationSentEvent.php
namespace App\Event\Notification;
```

4. **Créer le subscriber :**
```php
// src/EventSubscriber/NotificationEmailSubscriber.php
namespace App\EventSubscriber;
```

5. **Préfixer les routes :**
```php
#[Route('/api/notification/mailing')]
class NotificationMailingApiController
{
    // ...
}
```

**Résultat :** Aucun conflit avec le module Sale ! ✅

---

## 📝 Checklist d'Intégration

Avant d'intégrer un nouveau module :

- [ ] Vérifier les noms de services (éviter les doublons)
- [ ] Vérifier les routes API (utiliser des préfixes uniques)
- [ ] Vérifier les paramètres (utiliser des noms préfixés)
- [ ] Vérifier les événements (documenter les événements émis/écoutés)
- [ ] Tester en isolation (chaque module doit fonctionner indépendamment)

---

## 🎉 Résultat

**Architecture modulaire complète pour le module Sale :**

- ✅ Interface générique (`EmailServiceInterface`)
- ✅ Service modulaire (`SaleMailerService`)
- ✅ Système d'événements (4 événements)
- ✅ EventSubscriber centralisé
- ✅ Configuration préfixée
- ✅ Routes API préfixées
- ✅ Contrôleurs découplés

**Aucun conflit possible avec d'autres modules !** 🚀

---

## 📚 Documentation Complémentaire

- `explication/ARCHITECTURE_MODULAIRE.md` - Guide général d'architecture modulaire
- `explication/API_MAILING.md` - Documentation de l'API Mailing
- `explication/CONFIGURATION_GMAIL.md` - Configuration Gmail

---

**Toutes les solutions sont appliquées et fonctionnelles !** ✅

