# 📧 API Mailing - Documentation complète

## 🎯 Vue d'ensemble

L'API Mailing permet d'envoyer des emails via des endpoints REST. Tous les endpoints retournent des réponses JSON.

**Base URL :** `/api/mailing`

---

## 📋 Endpoints disponibles

### 1. Liste des endpoints
**GET** `/api/mailing`

Retourne la liste de tous les endpoints disponibles avec leur documentation.

**Réponse :**
```json
{
  "endpoints": [
    {
      "method": "POST",
      "path": "/api/mailing/test",
      "description": "Envoie un email de test",
      "body": {
        "to": "string (required)",
        "subject": "string (optional)",
        "message": "string (optional)"
      }
    },
    ...
  ]
}
```

---

### 2. Envoi d'email de test
**POST** `/api/mailing/test`

Envoie un email de test vers une adresse email.

**Body (JSON) :**
```json
{
  "to": "destinataire@example.com",
  "subject": "Test Email ArtEdu",
  "message": "Ceci est un email de test"
}
```

**Paramètres :**
- `to` (string, required) : Adresse email de destination
- `subject` (string, optional) : Sujet de l'email (défaut: "Test Email ArtEdu")
- `message` (string, optional) : Message de l'email

**Réponse succès (200) :**
```json
{
  "success": true,
  "message": "Email envoyé avec succès à destinataire@example.com"
}
```

**Réponse erreur (400/500) :**
```json
{
  "success": false,
  "error": "Message d'erreur"
}
```

**Exemple cURL :**
```bash
curl -X POST https://votre-domaine.com/api/mailing/test \
  -H "Content-Type: application/json" \
  -d '{
    "to": "test@example.com",
    "subject": "Test",
    "message": "Message de test"
  }'
```

---

### 3. Notification d'offre reçue
**POST** `/api/mailing/offer-received`

Envoie une notification au vendeur lorsqu'une nouvelle offre est reçue.

**Body (JSON) :**
```json
{
  "offer_id": 123
}
```

**Paramètres :**
- `offer_id` (integer, required) : ID de l'offre

**Réponse succès (200) :**
```json
{
  "success": true,
  "message": "Notification envoyée avec succès au vendeur."
}
```

**Exemple cURL :**
```bash
curl -X POST https://votre-domaine.com/api/mailing/offer-received \
  -H "Content-Type: application/json" \
  -d '{"offer_id": 123}'
```

---

### 4. Notification de changement de statut
**POST** `/api/mailing/status-updated`

Envoie une notification lorsqu'un statut de vente change.

**Body (JSON) :**
```json
{
  "sale_id": 456,
  "recipient_email": "client@example.com",
  "recipient_name": "Jean Dupont",
  "old_status": "en attente",
  "new_status": "en progress"
}
```

**Paramètres :**
- `sale_id` (integer, required) : ID de la vente
- `recipient_email` (string, required) : Email du destinataire
- `recipient_name` (string, required) : Nom du destinataire
- `old_status` (string, required) : Ancien statut
- `new_status` (string, required) : Nouveau statut

**Réponse succès (200) :**
```json
{
  "success": true,
  "message": "Notification de changement de statut envoyée avec succès."
}
```

---

### 5. Notification d'offre acceptée
**POST** `/api/mailing/offer-accepted`

Envoie une notification au client lorsque son offre est acceptée.

**Body (JSON) :**
```json
{
  "offer_id": 123
}
```

**Paramètres :**
- `offer_id` (integer, required) : ID de l'offre

**Réponse succès (200) :**
```json
{
  "success": true,
  "message": "Notification d'offre acceptée envoyée avec succès."
}
```

---

### 6. Notification d'offre refusée
**POST** `/api/mailing/offer-refused`

Envoie une notification au client lorsque son offre est refusée.

**Body (JSON) :**
```json
{
  "offer_id": 123
}
```

**Paramètres :**
- `offer_id` (integer, required) : ID de l'offre

**Réponse succès (200) :**
```json
{
  "success": true,
  "message": "Notification d'offre refusée envoyée avec succès."
}
```

---

### 7. Notification de paiement confirmé
**POST** `/api/mailing/payment-confirmed`

Envoie une notification lorsque le paiement est confirmé.

**Body (JSON) :**
```json
{
  "sale_id": 456,
  "recipient_email": "client@example.com",
  "recipient_name": "Jean Dupont",
  "amount": 1500.00
}
```

**Paramètres :**
- `sale_id` (integer, required) : ID de la vente
- `recipient_email` (string, required) : Email du destinataire
- `recipient_name` (string, required) : Nom du destinataire
- `amount` (float, required) : Montant payé

**Réponse succès (200) :**
```json
{
  "success": true,
  "message": "Notification de paiement confirmé envoyée avec succès."
}
```

---

## 🔐 Sécurité

### Authentification
Actuellement, l'API est accessible sans authentification. Pour la production, il est recommandé d'ajouter :
- Authentification par token (JWT)
- Rate limiting
- Validation des permissions

### Exemple avec authentification (à implémenter) :
```bash
curl -X POST https://votre-domaine.com/api/mailing/test \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"to": "test@example.com"}'
```

---

## 📊 Codes de réponse HTTP

| Code | Signification |
|------|--------------|
| 200 | Succès |
| 400 | Requête invalide (paramètres manquants) |
| 404 | Ressource introuvable (offre/vente) |
| 500 | Erreur serveur (problème d'envoi email) |

---

## 🧪 Tests

### Test avec Postman

1. **Collection Postman** : Importez cette collection :
```json
{
  "info": {
    "name": "ArtEdu Mailing API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Test Email",
      "request": {
        "method": "POST",
        "header": [{"key": "Content-Type", "value": "application/json"}],
        "body": {
          "mode": "raw",
          "raw": "{\"to\": \"test@example.com\", \"subject\": \"Test\", \"message\": \"Message de test\"}"
        },
        "url": {
          "raw": "{{base_url}}/api/mailing/test",
          "host": ["{{base_url}}"],
          "path": ["api", "mailing", "test"]
        }
      }
    }
  ]
}
```

### Test avec cURL

```bash
# Test email simple
curl -X POST http://localhost:8000/api/mailing/test \
  -H "Content-Type: application/json" \
  -d '{"to": "hadjamorrached@gmail.com", "subject": "Test API", "message": "Test réussi !"}'
```

---

## 🔄 Intégration avec le module User (futur)

L'API est conçue pour être facilement intégrée avec un module User :

1. **Authentification** : Ajouter vérification du token JWT
2. **Permissions** : Vérifier que l'utilisateur peut envoyer l'email
3. **User ID** : Utiliser `$this->getUser()` au lieu des emails en string

**Exemple futur :**
```php
#[Route('/test', name: 'test', methods: ['POST'])]
#[IsGranted('ROLE_USER')]
public function testEmail(Request $request, MailerService $mailerService): JsonResponse
{
    $user = $this->getUser(); // Récupère l'utilisateur connecté
    // ...
}
```

---

## 📝 Notes importantes

1. **Configuration Gmail** : Assurez-vous que la configuration Gmail est correcte dans `.env`
2. **Limites** : Gmail limite à 500 emails/jour pour les comptes gratuits
3. **Logs** : Les erreurs sont loggées dans `var/log/dev.log`
4. **Templates** : Les emails utilisent des templates Twig dans `templates/emails/`

---

## 🚀 Utilisation dans le code

### Depuis un contrôleur Symfony

```php
use App\Service\MailerService;

class MonController extends AbstractController
{
    public function maMethode(MailerService $mailerService)
    {
        // Envoyer une notification
        $mailerService->sendOfferReceivedNotification($offer, $sale);
    }
}
```

### Depuis une API externe

```javascript
// JavaScript/TypeScript
fetch('https://votre-domaine.com/api/mailing/test', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    to: 'destinataire@example.com',
    subject: 'Test',
    message: 'Message de test'
  })
})
.then(response => response.json())
.then(data => console.log(data));
```

---

## ✅ Checklist d'implémentation

- [x] Endpoint test email
- [x] Endpoint offre reçue
- [x] Endpoint statut mis à jour
- [x] Endpoint offre acceptée
- [x] Endpoint offre refusée
- [x] Endpoint paiement confirmé
- [x] Documentation complète
- [ ] Authentification (à ajouter)
- [ ] Rate limiting (à ajouter)
- [ ] Tests unitaires (à ajouter)

---

**L'API Mailing est prête à être utilisée !** 🎉

