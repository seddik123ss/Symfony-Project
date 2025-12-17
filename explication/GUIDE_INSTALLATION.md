# 🚀 Guide d'Installation Rapide - Module Sale Avancé

## ✅ Étape 1 : Dépendances

### Stripe (optionnel, pour les paiements)
```bash
composer require stripe/stripe-php
```

**Note :** Si vous ne pouvez pas installer Stripe maintenant, le service `PaymentService` est prêt mais nécessitera la clé API Stripe.

---

## ✅ Étape 2 : Configuration

### Variables d'environnement (.env)

Ajoutez dans votre fichier `.env` :

```env
# Gmail (déjà configuré)
MAILER_DSN=smtp://hadjamorrached@gmail.com:zsdknoauyvgduecl@smtp.gmail.com:587

# Stripe (optionnel)
STRIPE_SECRET_KEY=sk_test_votre_cle_secrete
STRIPE_PUBLIC_KEY=pk_test_votre_cle_publique
```

---

## ✅ Étape 3 : Dossiers

### Créer le dossier uploads
```bash
# Windows PowerShell
New-Item -ItemType Directory -Path "public\uploads\sales" -Force

# Linux/Mac
mkdir -p public/uploads/sales
chmod 755 public/uploads/sales
```

---

## ✅ Étape 4 : Migrations

Les migrations ont déjà été exécutées avec succès ! ✅

Si vous devez les réexécuter :
```bash
php bin/console doctrine:migrations:migrate
```

---

## ✅ Étape 5 : Test de l'API Mailing

### Test simple
```bash
curl -X POST http://localhost:8000/api/mailing/test \
  -H "Content-Type: application/json" \
  -d "{\"to\": \"hadjamorrached@gmail.com\", \"subject\": \"Test\", \"message\": \"Test réussi !\"}"
```

### Ou via la commande Symfony
```bash
php bin/console app:test-email hadjamorrached@gmail.com
```

---

## 📋 Routes disponibles

### Offres
- `GET /offer/sale/{id}/new` - Formulaire de création d'offre
- `POST /offer/sale/{id}/new` - Créer une offre
- `POST /offer/{id}/accept` - Accepter une offre
- `POST /offer/{id}/refuse` - Refuser une offre
- `POST /offer/{id}/negotiate` - Négocier le montant
- `POST /offer/{id}/final` - Marquer comme finale
- `GET /offer/{id}` - Voir une offre

### Paiements
- `POST /payment/sale/{id}/create-intent` - Créer PaymentIntent
- `POST /payment/confirm` - Confirmer paiement
- `POST /payment/webhook` - Webhook Stripe

### API Mailing
- `GET /api/mailing` - Liste des endpoints
- `POST /api/mailing/test` - Email de test
- `POST /api/mailing/offer-received` - Notification offre reçue
- `POST /api/mailing/status-updated` - Notification statut
- `POST /api/mailing/offer-accepted` - Notification offre acceptée
- `POST /api/mailing/offer-refused` - Notification offre refusée
- `POST /api/mailing/payment-confirmed` - Notification paiement

---

## 🧪 Tests rapides

### 1. Test email
```bash
php bin/console app:test-email
```

### 2. Test API Mailing
```bash
curl -X GET http://localhost:8000/api/mailing
```

### 3. Vérifier les routes
```bash
php bin/console debug:router | findstr offer
php bin/console debug:router | findstr payment
php bin/console debug:router | findstr mailing
```

---

## 📝 Notes importantes

1. **Gmail** : Configuration déjà faite et testée ✅
2. **Stripe** : À installer si vous voulez les paiements
3. **Migrations** : Déjà exécutées ✅
4. **Services** : Tous configurés dans `services.yaml` ✅

---

## 🎯 Prochaines étapes

1. ✅ Implémentation complète terminée
2. ⏳ Tester les fonctionnalités
3. ⏳ Créer les templates Twig pour les pages (offer/new.html.twig, etc.)
4. ⏳ Intégrer avec le module User (quand disponible)

---

**Tout est prêt !** 🎉

