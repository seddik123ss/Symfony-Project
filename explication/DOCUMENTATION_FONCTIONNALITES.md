# 📚 Documentation Complète des Fonctionnalités - Module Sale

## 📋 Table des Matières

1. [Vue d'Ensemble](#vue-densemble)
2. [Fonctionnalités Publiques](#fonctionnalités-publiques)
3. [Fonctionnalités Administration](#fonctionnalités-administration)
4. [Système d'Offres](#système-doffres)
5. [Système de Paiement](#système-de-paiement)
6. [Gestion des Images](#gestion-des-images)
7. [Système d'Événements](#système-dévénements)
8. [Emplacement du Code](#emplacement-du-code)

---

## 🎯 Vue d'Ensemble

Le module Sale est un système complet de gestion de ventes d'œuvres d'art avec :
- ✅ Galerie publique d'œuvres
- ✅ Système de vente/échange/tickets
- ✅ Gestion d'offres bidirectionnelles
- ✅ Intégration Stripe pour les paiements
- ✅ Gestion d'images multiples
- ✅ Système d'événements modulaire
- ✅ API Mailing complète

**Technologies :** Symfony 6.x, Doctrine ORM, Twig, Bootstrap 5, Stripe API, Gmail SMTP

---

## 🌐 Fonctionnalités Publiques

### 1. Page d'Accueil - Galerie d'Œuvres

**Route :** `GET /`  
**Contrôleur :** `HomeSaleController::index()`  
**Template :** `templates/homeSale/index.html.twig`

#### Fonctionnalités :
- ✅ Affichage de toutes les ventes disponibles
- ✅ Filtrage par type (Vente, Ticket, Échange)
- ✅ Carousel Swiper pour navigation
- ✅ Bouton "Acheter" direct vers paiement
- ✅ Bouton "Faire une offre"
- ✅ Exclusion automatique des ventes vendues

#### Logique Métier :
```php
// src/Controller/HomeSaleController.php (lignes 14-42)
// Filtre les ventes : exclut VENDUE, PAYER, ANNULE, REFUSER, PAYEMENT_EN_COURS
// Affiche uniquement : EN_ATTENTE, DISPONIBLE, EN_PROGRESS
```

#### Où trouver le code :
- **Contrôleur :** `src/Controller/HomeSaleController.php`
- **Template :** `templates/homeSale/index.html.twig`
- **Repository :** `src/Repository/SaleRepository.php` (méthode `findAll()`)

---

### 2. Page de Détails d'une Vente

**Route :** `GET /sale/{id}/details`  
**Contrôleur :** `SaleController::details()`  
**Template :** `templates/sale/details.html.twig`

#### Fonctionnalités :
- ✅ Affichage de l'image principale en grand
- ✅ Galerie d'images secondaires (cliquables, agrandissables)
- ✅ Informations complètes de la vente
- ✅ Liste des offres reçues
- ✅ Bouton "Acheter maintenant" (si prix défini)
- ✅ Bouton "Faire une offre"
- ✅ Bouton "Procéder au paiement" (si offre acceptée)

#### Logique Métier :
```php
// src/Controller/SaleController.php (lignes 109-127)
// Récupère : Sale, Offers, AcceptedOffer, Images triées
// Passe au template : sale, offers, acceptedOffer, images
```

#### Où trouver le code :
- **Contrôleur :** `src/Controller/SaleController.php` (méthode `details()`)
- **Template :** `templates/sale/details.html.twig`
- **Entité :** `src/Entity/Sale.php` (méthode `getSortedImages()`)

---

## 🔧 Fonctionnalités Administration

### 1. Liste des Ventes

**Route :** `GET /sale`  
**Contrôleur :** `SaleController::index()`  
**Template :** `templates/sale/index.html.twig`

#### Fonctionnalités :
- ✅ Tableau de toutes les ventes
- ✅ Affichage image principale + images secondaires
- ✅ Statistiques par type (Vente, Ticket, Échange)
- ✅ Filtrage par statut
- ✅ Recherche
- ✅ Actions : Voir, Modifier, Supprimer

#### Où trouver le code :
- **Contrôleur :** `src/Controller/SaleController.php` (méthode `index()`)
- **Template :** `templates/sale/index.html.twig`
- **Repository :** `src/Repository/SaleRepository.php` (méthode `findAllOrderedByStatus()`)

---

### 2. Création d'une Vente

**Route :** `GET|POST /sale/new`  
**Contrôleur :** `SaleController::new()`  
**Template :** `templates/sale/new.html.twig`  
**Form :** `src/Form/SaleType.php`

#### Fonctionnalités :
- ✅ Formulaire complet avec validation
- ✅ Upload image principale
- ✅ Upload galerie d'images (jusqu'à 5)
- ✅ Prévisualisation des images
- ✅ Validation côté serveur et client

#### Champs du formulaire :
- Type de vente (obligatoire)
- Titre (obligatoire)
- Description
- Montant
- Contact (Email/Téléphone)
- Localisation
- Image principale
- Galerie d'images (jusqu'à 5)

#### Où trouver le code :
- **Contrôleur :** `src/Controller/SaleController.php` (méthode `new()`)
- **Template :** `templates/sale/new.html.twig`
- **Form :** `src/Form/SaleType.php`
- **Service Upload :** `src/Service/SaleImageUploader.php`

---

### 3. Modification d'une Vente

**Route :** `GET|POST /sale/{id}/edit`  
**Contrôleur :** `SaleController::edit()`  
**Template :** `templates/sale/edit.html.twig`

#### Fonctionnalités :
- ✅ Même formulaire que création
- ✅ Prévisualisation des images existantes
- ✅ Remplacement d'images
- ✅ Modification du statut (en édition uniquement)

#### Où trouver le code :
- **Contrôleur :** `src/Controller/SaleController.php` (méthode `edit()`)
- **Template :** `templates/sale/edit.html.twig`

---

### 4. Détails d'une Vente (Admin)

**Route :** `GET /sale/{id}`  
**Contrôleur :** `SaleController::show()`  
**Template :** `templates/sale/show.html.twig`

#### Fonctionnalités :
- ✅ Affichage image principale en grand
- ✅ Galerie d'images secondaires
- ✅ Toutes les informations de la vente
- ✅ Actions : Modifier, Supprimer

#### Où trouver le code :
- **Contrôleur :** `src/Controller/SaleController.php` (méthode `show()`)
- **Template :** `templates/sale/show.html.twig`

---

### 5. Gestion des Offres

**Route :** `GET /sale/{id}/offers`  
**Contrôleur :** `SaleController::offers()`  
**Template :** `templates/sale/offers.html.twig`

#### Fonctionnalités :
- ✅ Liste des offres par statut (pending, negotiating, accepted, refused)
- ✅ Actions : Accepter, Refuser, Négocier
- ✅ Formulaire de contre-offre
- ✅ Affichage des détails de chaque offre

#### Où trouver le code :
- **Contrôleur :** `src/Controller/SaleController.php` (méthode `offers()`)
- **Template :** `templates/sale/offers.html.twig`
- **Repository :** `src/Repository/OfferRepository.php`

---

## 💰 Système d'Offres

### 1. Création d'une Offre

**Route :** `GET|POST /offer/sale/{id}/new`  
**Contrôleur :** `OfferController::new()`  
**Template :** `templates/offer/new.html.twig`  
**Form :** `src/Form/OfferType.php`

#### Fonctionnalités :
- ✅ Formulaire avec montant offert
- ✅ Informations client (nom, email, téléphone)
- ✅ Message optionnel
- ✅ Validation des montants
- ✅ Envoi automatique d'email au vendeur (via événement)

#### Où trouver le code :
- **Contrôleur :** `src/Controller/OfferController.php` (méthode `new()`)
- **Template :** `templates/offer/new.html.twig`
- **Form :** `src/Form/OfferType.php`
- **Événement :** `src/Event/Sale/OfferReceivedEvent.php`

---

### 2. Acceptation d'une Offre

**Route :** `POST /offer/{id}/accept`  
**Contrôleur :** `OfferController::accept()`

#### Fonctionnalités :
- ✅ Acceptation par le vendeur
- ✅ Si client a aussi accepté → Offre mutuellement acceptée
- ✅ Changement de statut de la vente → VENDUE
- ✅ Envoi d'email au client (via événement)

#### Logique Métier :
```php
// src/Controller/OfferController.php (lignes 70-107)
// Si clientAccepted && vendorAccepted → status = 'accepted', sale.status = VENDUE
// Sinon → status = 'negotiating'
```

#### Où trouver le code :
- **Contrôleur :** `src/Controller/OfferController.php` (méthode `accept()`)
- **Événement :** `src/Event/Sale/OfferAcceptedEvent.php`

---

### 3. Refus d'une Offre

**Route :** `POST /offer/{id}/refuse`  
**Contrôleur :** `OfferController::refuse()`

#### Fonctionnalités :
- ✅ Refus par le vendeur
- ✅ Changement de statut → 'refused'
- ✅ Envoi d'email au client (via événement)

#### Où trouver le code :
- **Contrôleur :** `src/Controller/OfferController.php` (méthode `refuse()`)
- **Événement :** `src/Event/Sale/OfferRefusedEvent.php`

---

### 4. Négociation d'une Offre

**Route :** `POST /offer/{id}/negotiate`  
**Contrôleur :** `OfferController::negotiate()`

#### Fonctionnalités :
- ✅ Contre-offre du vendeur
- ✅ Changement de statut → 'negotiating'
- ✅ Le client peut accepter ou refuser la contre-offre

#### Où trouver le code :
- **Contrôleur :** `src/Controller/OfferController.php` (méthode `negotiate()`)

---

### 5. Offre Finale

**Route :** `POST /offer/{id}/final`  
**Contrôleur :** `OfferController::final()`

#### Fonctionnalités :
- ✅ Marquer une offre comme finale
- ✅ Empêche toute nouvelle négociation

#### Où trouver le code :
- **Contrôleur :** `src/Controller/OfferController.php` (méthode `final()`)

---

## 💳 Système de Paiement

### 1. Page de Paiement

**Route :** `GET /payment/sale/{id}`  
**Contrôleur :** `PaymentController::paymentPage()`  
**Template :** `templates/sale/payment.html.twig`

#### Fonctionnalités :
- ✅ Formulaire Stripe intégré
- ✅ Support achat direct (sans offre)
- ✅ Support paiement via offre acceptée
- ✅ Formulaire nom/email pour achat direct
- ✅ Affichage du montant à payer

#### Où trouver le code :
- **Contrôleur :** `src/Controller/PaymentController.php` (méthode `paymentPage()`)
- **Template :** `templates/sale/payment.html.twig`

---

### 2. Création d'un PaymentIntent

**Route :** `POST /payment/sale/{id}/create-intent`  
**Contrôleur :** `PaymentController::createPaymentIntent()`

#### Fonctionnalités :
- ✅ Création PaymentIntent Stripe
- ✅ Support achat direct et paiement via offre
- ✅ Métadonnées pour traçabilité

#### Où trouver le code :
- **Contrôleur :** `src/Controller/PaymentController.php` (méthode `createPaymentIntent()`)
- **Service :** `src/Service/PaymentService.php` (méthodes `createPaymentIntent()`, `createDirectPurchaseIntent()`)

---

### 3. Confirmation de Paiement

**Route :** `POST /payment/confirm`  
**Contrôleur :** `PaymentController::confirmPayment()`

#### Fonctionnalités :
- ✅ Vérification du paiement Stripe
- ✅ Génération de facture PDF
- ✅ Mise à jour statut → VENDUE
- ✅ Envoi email avec facture (via événement)
- ✅ Téléchargement automatique de la facture

#### Logique Métier :
```php
// src/Controller/PaymentController.php (lignes 133-230)
// 1. Vérifie paymentIntent.status === 'succeeded'
// 2. Génère facture PDF
// 3. Met à jour sale.status = VENDUE
// 4. Émet PaymentConfirmedEvent
```

#### Où trouver le code :
- **Contrôleur :** `src/Controller/PaymentController.php` (méthode `confirmPayment()`)
- **Service Facture :** `src/Service/InvoiceService.php`
- **Événement :** `src/Event/Sale/PaymentConfirmedEvent.php`

---

### 4. Webhook Stripe

**Route :** `POST /payment/webhook`  
**Contrôleur :** `PaymentController::webhook()`

#### Fonctionnalités :
- ✅ Réception des événements Stripe
- ✅ Mise à jour automatique du statut

#### Où trouver le code :
- **Contrôleur :** `src/Controller/PaymentController.php` (méthode `webhook()`)

---

## 🖼️ Gestion des Images

### 1. Image Principale

**Fonctionnalités :**
- ✅ Upload d'une image principale par vente
- ✅ Stockage : `public/uploads/sales/`
- ✅ Formats : JPEG, PNG, GIF, WebP
- ✅ Taille max : 5MB
- ✅ Affichage en grand dans les détails

#### Où trouver le code :
- **Service :** `src/Service/SaleImageUploader.php` (méthode `upload()`)
- **Entité :** `src/Entity/Sale.php` (propriété `$image`)

---

### 2. Galerie d'Images (Multiples)

**Fonctionnalités :**
- ✅ Upload jusqu'à 5 images par vente
- ✅ Image principale marquée (`isPrimary = true`)
- ✅ Tri par `sortOrder`
- ✅ Affichage : principale en grand, secondaires en petit
- ✅ Images cliquables et agrandissables

#### Où trouver le code :
- **Service :** `src/Service/SaleImageUploader.php` (méthode `uploadMultiple()`)
- **Entité :** `src/Entity/SaleImage.php`
- **Entité Sale :** `src/Entity/Sale.php` (méthode `getSortedImages()`)

---

## 📧 Système d'Événements

### Architecture Modulaire

Le module utilise le système d'événements Symfony pour découpler les services.

#### Événements Disponibles :

1. **PaymentConfirmedEvent**
   - **Déclenché :** Lors de la confirmation d'un paiement
   - **Fichier :** `src/Event/Sale/PaymentConfirmedEvent.php`
   - **Écouté par :** `SaleEmailSubscriber::onPaymentConfirmed()`

2. **OfferReceivedEvent**
   - **Déclenché :** Lors de la réception d'une offre
   - **Fichier :** `src/Event/Sale/OfferReceivedEvent.php`
   - **Écouté par :** `SaleEmailSubscriber::onOfferReceived()`

3. **OfferAcceptedEvent**
   - **Déclenché :** Lors de l'acceptation d'une offre
   - **Fichier :** `src/Event/Sale/OfferAcceptedEvent.php`
   - **Écouté par :** `SaleEmailSubscriber::onOfferAccepted()`

4. **OfferRefusedEvent**
   - **Déclenché :** Lors du refus d'une offre
   - **Fichier :** `src/Event/Sale/OfferRefusedEvent.php`
   - **Écouté par :** `SaleEmailSubscriber::onOfferRefused()`

#### EventSubscriber :

**Fichier :** `src/EventSubscriber/SaleEmailSubscriber.php`

- ✅ Écoute tous les événements Sale
- ✅ Gère l'envoi des emails de manière centralisée
- ✅ Automatiquement enregistré par Symfony

#### Où trouver le code :
- **Événements :** `src/Event/Sale/*.php`
- **Subscriber :** `src/EventSubscriber/SaleEmailSubscriber.php`
- **Service Email :** `src/Service/Sale/SaleMailerService.php`

---

## 📍 Emplacement du Code

### Structure des Dossiers

```
src/
├── Controller/
│   ├── HomeSaleController.php          # Page d'accueil publique
│   ├── SaleController.php              # CRUD ventes (admin)
│   ├── OfferController.php             # Gestion des offres
│   ├── PaymentController.php           # Paiements Stripe
│   └── Api/
│       └── MailingApiController.php     # API Mailing
│
├── Entity/
│   ├── Sale.php                        # Entité principale
│   ├── Offer.php                       # Entité offre
│   ├── SaleImage.php                   # Entité image
│   └── SaleItem.php                    # Entité item (legacy)
│
├── Repository/
│   ├── SaleRepository.php              # Requêtes Sale
│   ├── OfferRepository.php             # Requêtes Offer
│   └── SaleImageRepository.php         # Requêtes SaleImage
│
├── Service/
│   ├── Sale/
│   │   └── SaleMailerService.php       # Service email modulaire
│   ├── PaymentService.php              # Service Stripe
│   ├── InvoiceService.php              # Génération factures PDF
│   └── SaleImageUploader.php           # Upload images
│
├── Form/
│   ├── SaleType.php                    # Formulaire vente
│   └── OfferType.php                   # Formulaire offre
│
├── Event/
│   └── Sale/
│       ├── PaymentConfirmedEvent.php
│       ├── OfferReceivedEvent.php
│       ├── OfferAcceptedEvent.php
│       └── OfferRefusedEvent.php
│
├── EventSubscriber/
│   └── SaleEmailSubscriber.php         # Gestionnaire emails
│
└── Enum/
    ├── SaleType.php                    # Type : VENTE, TICKET, ECHANGE
    └── SaleStatus.php                  # Statut : EN_ATTENTE, VENDUE, etc.
```

### Templates

```
templates/
├── homeSale/
│   └── index.html.twig                 # Page d'accueil publique
│
├── sale/
│   ├── index.html.twig                 # Liste ventes (admin)
│   ├── new.html.twig                   # Création vente
│   ├── edit.html.twig                  # Modification vente
│   ├── show.html.twig                  # Détails vente (admin)
│   ├── details.html.twig               # Détails vente (public)
│   ├── offers.html.twig                # Gestion offres
│   ├── payment.html.twig               # Page paiement
│   └── _form.html.twig                 # Partial formulaire
│
├── offer/
│   ├── new.html.twig                   # Création offre
│   └── show.html.twig                  # Détails offre
│
└── emails/
    ├── offer_received.html.twig
    ├── offer_accepted.html.twig
    ├── offer_refused.html.twig
    ├── payment_confirmed.html.twig
    └── direct_purchase_confirmation.html.twig
```

---

## 🔄 Flux Complets

### Flux : Achat Direct

1. **Page d'accueil** → Clic "Acheter"
2. **Page paiement** → Remplir nom/email + carte bancaire
3. **Stripe** → Traitement paiement
4. **PaymentController** → Confirmation → Émet `PaymentConfirmedEvent`
5. **SaleEmailSubscriber** → Reçoit événement → Génère facture → Envoie email
6. **Frontend** → Télécharge facture automatiquement
7. **Statut** → Sale.status = VENDUE
8. **Page d'accueil** → L'œuvre disparaît automatiquement

### Flux : Offre et Négociation

1. **Page détails** → Clic "Faire une offre"
2. **Formulaire offre** → Remplir montant + infos client
3. **OfferController** → Crée offre → Émet `OfferReceivedEvent`
4. **SaleEmailSubscriber** → Envoie email au vendeur
5. **Page gestion offres** → Vendeur voit l'offre
6. **Vendeur** → Accepte ou fait contre-offre
7. **Si acceptée** → Émet `OfferAcceptedEvent` → Email au client
8. **Client** → Peut procéder au paiement

---

## 📊 Statistiques

- **Routes publiques :** 2 (`/`, `/sale/{id}/details`)
- **Routes admin :** 8 (`/sale/*`)
- **Routes offre :** 6 (`/offer/*`)
- **Routes paiement :** 4 (`/payment/*`)
- **Routes API :** 7 (`/api/sale/mailing/*`)
- **Total :** 27 routes

- **Entités :** 4 (Sale, Offer, SaleImage, SaleItem)
- **Services :** 4 (SaleMailerService, PaymentService, InvoiceService, SaleImageUploader)
- **Événements :** 4
- **Templates :** 15+

---

**Documentation complète - Toutes les fonctionnalités sont documentées avec leur emplacement dans le code !** ✅

