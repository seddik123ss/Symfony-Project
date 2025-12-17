# 🤝 Guide de Collaboration Git - Module Sale

## 📋 Table des Matières

1. [Préparation du Repository](#préparation-du-repository)
2. [Push du Projet](#push-du-projet)
3. [Merge avec une Autre Branche](#merge-avec-une-autre-branche)
4. [Résolution des Conflits](#résolution-des-conflits)
5. [Workflow Recommandé](#workflow-recommandé)
6. [Scénarios Complets](#scénarios-complets-de-merge)

---

## 🎯 Vue d'Ensemble - Votre Situation

**Votre situation :**
- ✅ Vous avez un projet complet (Module Sale)
- ✅ Vous voulez l'ajouter dans un repository d'un collaborateur
- ✅ Vous n'êtes **PAS le propriétaire** du repository (pas le main)
- ✅ Vous devez créer une **nouvelle branche** pour votre travail
- ✅ Vous voulez merger votre branche avec une autre branche (ex: `develop`, `integration`)

**Workflow général :**
1. Cloner le repository du collaborateur
2. Créer une nouvelle branche (`feature/sale-module`)
3. Ajouter votre code dans cette branche
4. Push votre branche
5. Merger avec une autre branche (localement ou via Pull Request)

---

## 🚀 Préparation du Repository

### Étape 1 : Vérifier le .gitignore

Assurez-vous que votre `.gitignore` contient :

```gitignore
# Fichiers sensibles (NE JAMAIS COMMITER)
.env
.env.local
.env.*.local

# Dossiers de cache
/var/
/vendor/
/node_modules/

# Fichiers de configuration locaux
/config/packages/*.yaml.local

# Fichiers uploadés (optionnel, selon votre besoin)
/public/uploads/
/public/invoices/

# Fichiers IDE
.idea/
.vscode/
*.swp
*.swo
```

### Étape 2 : Créer un fichier .env.example

Créez un fichier `.env.example` avec les variables nécessaires (sans les valeurs sensibles) :

```env
# Database
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/artedu?serverVersion=15&charset=utf8"

# Mailer
MAILER_DSN=smtp://email:password@smtp.gmail.com:587?verify_peer=0

# Stripe (optionnel)
STRIPE_SECRET_KEY=sk_test_...
STRIPE_PUBLIC_KEY=pk_test_...
```

### Étape 3 : Initialiser Git (si pas déjà fait)

```bash
# Initialiser Git
git init

# Ajouter tous les fichiers
git add .

# Premier commit
git commit -m "Initial commit: Module Sale complet avec fonctionnalités avancées"
```

---

## 📤 Push du Projet

### Scénario : Ajouter votre Module dans un Repository Existant (Vous n'êtes pas le main)

**Situation :** Le repository appartient à votre collaborateur, vous voulez ajouter votre module Sale dans une nouvelle branche.

#### Étape 1 : Cloner le Repository du Collaborateur

```bash
# Cloner le repository (vous n'avez pas les droits main, c'est normal)
git clone https://github.com/votre-collaborateur/nom-du-repo.git
cd nom-du-repo

# Vérifier les branches existantes
git branch -a
```

#### Étape 2 : Créer une Nouvelle Branche pour votre Module

```bash
# Se placer sur la branche principale (généralement main ou master)
git checkout main
# OU si la branche principale s'appelle autrement :
# git checkout develop

# Récupérer les dernières modifications
git pull origin main

# Créer une NOUVELLE branche pour votre module Sale
git checkout -b feature/sale-module

# Vérifier que vous êtes sur la bonne branche
git branch
# Vous devriez voir : * feature/sale-module
```

#### Étape 3 : Copier votre Code dans cette Branche

**Option A : Copier manuellement**
```bash
# Depuis votre projet ArtEduOrg, copier tous les fichiers
# dans le repository cloné (sauf .git et node_modules)

# Puis ajouter les fichiers
git add .

# Commit
git commit -m "feat: Ajout du module Sale complet

- Système de vente d'œuvres d'art
- Gestion des offres bidirectionnelles
- Intégration Stripe
- API Mailing
- Architecture modulaire avec événements"
```

**Option B : Utiliser git remote (Recommandé si vous avez déjà un repo)**
```bash
# Depuis votre projet ArtEduOrg
cd /chemin/vers/votre/projet/ArtEduOrg

# Ajouter le repository du collaborateur comme remote
git remote add collaborateur https://github.com/votre-collaborateur/nom-du-repo.git

# Créer une branche locale pour le module Sale
git checkout -b feature/sale-module

# Push cette branche vers le repository du collaborateur
git push -u collaborateur feature/sale-module
```

#### Étape 4 : Push de votre Branche

```bash
# Si vous êtes dans le repository cloné
git push -u origin feature/sale-module

# Si vous avez utilisé l'option B (remote)
# La branche est déjà pushée
```

#### Étape 5 : Vérifier sur GitHub/GitLab

- Aller sur la plateforme
- Vérifier que la branche `feature/sale-module` existe
- Vérifier que tous vos fichiers sont présents

---

## 🔀 Merge avec une Autre Branche

### Scénario : Merger votre Branche avec une Autre Branche

**Situation :** Vous avez créé `feature/sale-module` et vous voulez la merger avec une autre branche (ex: `develop`, `integration`, ou une branche du collaborateur).

---

### Option 1 : Merge Local (Sur votre PC)

#### Étape 1 : Récupérer toutes les branches

```bash
# Dans le repository cloné
cd nom-du-repo

# Récupérer toutes les branches distantes
git fetch origin

# Voir toutes les branches disponibles
git branch -a
```

#### Étape 2 : Se placer sur la branche cible

```bash
# Exemple : merger dans la branche "develop"
git checkout develop

# OU si la branche cible s'appelle "integration"
# git checkout integration

# Récupérer les dernières modifications
git pull origin develop
```

#### Étape 3 : Merger votre branche

```bash
# Merger feature/sale-module dans develop
git merge feature/sale-module

# Si conflits, résoudre (voir section Résolution des Conflits)
# Puis continuer :
git add .
git commit -m "Merge: Intégration du module Sale dans develop"
```

#### Étape 4 : Push du merge

```bash
# Push la branche develop avec votre merge
git push origin develop
```

---

### Option 2 : Merge via Git (Sans Pull Request)

#### Si vous avez les droits sur le repository :

```bash
# Se placer sur la branche cible
git checkout develop
git pull origin develop

# Merger votre branche
git merge feature/sale-module

# Résoudre les conflits si nécessaire
# Puis push
git push origin develop
```

---

### Option 3 : Merge via Pull Request (Recommandé)

#### Sur GitHub/GitLab :

1. **Créer une Pull Request** :
   - Aller sur la plateforme
   - Cliquer sur "New Pull Request" ou "New Merge Request"
   - **Source :** `feature/sale-module` (votre branche)
   - **Destination :** `develop` (ou la branche cible)
   - Ajouter une description

2. **Description de la PR** :
```markdown
## Module Sale - Fonctionnalités Avancées

### Ce qui est inclus :
- ✅ Système de vente d'œuvres d'art
- ✅ Gestion des offres bidirectionnelles
- ✅ Intégration Stripe complète
- ✅ Gestion d'images multiples
- ✅ Système d'événements modulaire
- ✅ API Mailing complète

### Structure :
- Routes : `/sale/*`, `/offer/*`, `/payment/*`, `/api/sale/mailing/*`
- Services : `SaleMailerService`, `PaymentService`, `InvoiceService`
- Événements : `PaymentConfirmedEvent`, `OfferReceivedEvent`, etc.

### Configuration nécessaire :
- MAILER_DSN dans .env
- STRIPE_SECRET_KEY et STRIPE_PUBLIC_KEY (optionnel)
```

3. **Review et Merge** :
   - Le collaborateur review le code
   - Merge via l'interface web
   - La branche `develop` contiendra maintenant votre module

---

### Option 4 : Merger Deux Branches Différentes

#### Si vous voulez merger deux branches qui ne sont pas les vôtres :

```bash
# Exemple : Merger "feature/notification-module" avec "feature/sale-module"

# Se placer sur votre branche
git checkout feature/sale-module

# Récupérer la branche du collaborateur
git fetch origin feature/notification-module

# Merger la branche du collaborateur dans la vôtre
git merge origin/feature/notification-module

# Résoudre les conflits
# Puis push
git push origin feature/sale-module
```

---

### Workflow Complet : Exemple Pratique

**Scénario :** Vous voulez ajouter votre module Sale dans le repository du collaborateur qui a déjà une branche `develop`.

```bash
# 1. Cloner le repository
git clone https://github.com/collaborateur/projet.git
cd projet

# 2. Voir les branches existantes
git branch -a
# * main
#   develop
#   feature/notification-module

# 3. Se placer sur develop pour voir ce qu'il y a
git checkout develop
git pull origin develop

# 4. Créer votre branche depuis develop
git checkout -b feature/sale-module

# 5. Copier votre code (depuis votre projet ArtEduOrg)
# ... copier tous les fichiers ...

# 6. Ajouter et commiter
git add .
git commit -m "feat: Ajout du module Sale"

# 7. Push votre branche
git push -u origin feature/sale-module

# 8. Sur GitHub : Créer Pull Request
# feature/sale-module → develop

# 9. Après review et merge de la PR :
# Votre code est maintenant dans develop !

# 10. Mettre à jour votre branche locale
git checkout develop
git pull origin develop
```

---

## ⚠️ Résolution des Conflits

### Identifier les Conflits

Lors du merge, Git peut signaler des conflits :

```
Auto-merging config/services.yaml
CONFLICT (content): Merge conflict in config/services.yaml
```

### Résoudre les Conflits

#### 1. Ouvrir le fichier en conflit

Vous verrez des marqueurs :
```yaml
<<<<<<< HEAD
# Configuration du collaborateur
parameter:
    notification.mailer.from_email: 'notif@example.com'
=======
# Configuration du module Sale
parameters:
    sale.mailer.from_email: 'hadjamorrached@gmail.com'
>>>>>>> feature/sale-module
```

#### 2. Résoudre manuellement

**Solution : Garder les deux configurations**
```yaml
parameters:
    # Module Notification (collaborateur)
    notification.mailer.from_email: 'notif@example.com'
    
    # Module Sale
    sale.mailer.from_email: 'hadjamorrached@gmail.com'
```

#### 3. Marquer comme résolu

```bash
# Après avoir résolu tous les conflits
git add config/services.yaml

# Continuer le merge
git commit -m "Merge: Résolution des conflits entre modules"
```

### Conflits Courants et Solutions

#### Conflit dans `config/services.yaml`

**Problème :** Deux modules définissent des services

**Solution :** Fusionner les configurations
```yaml
services:
    # Module Sale
    App\Service\Sale\SaleMailerService:
        arguments:
            $fromEmail: '%sale.mailer.from_email%'
    
    # Module Notification (collaborateur)
    App\Service\Notification\NotificationMailerService:
        arguments:
            $fromEmail: '%notification.mailer.from_email%'
```

#### Conflit dans `composer.json`

**Problème :** Dépendances différentes

**Solution :** Fusionner les dépendances
```bash
# Résoudre automatiquement
composer update

# Vérifier les conflits
composer check-platform-reqs
```

#### Conflit dans les Routes

**Problème :** Routes en conflit (même path)

**Solution :** Utiliser des préfixes différents
```php
// Module Sale
#[Route('/api/sale/mailing')]
class SaleMailingApiController { }

// Module Notification (collaborateur)
#[Route('/api/notification/mailing')]
class NotificationMailingApiController { }
```

---

## 🔄 Workflow Recommandé

### Workflow Git Flow

```
main (production)
  │
  ├── develop (développement)
  │     │
  │     ├── feature/sale-module (votre module)
  │     └── feature/notification-module (collaborateur)
  │
  └── hotfix/* (corrections urgentes)
```

### Commandes Essentielles

#### 1. Récupérer les dernières modifications
```bash
# Se placer sur la branche principale
git checkout main

# Récupérer les modifications
git pull origin main
```

#### 2. Créer une branche pour votre travail
```bash
# Créer une nouvelle branche depuis main
git checkout -b feature/ma-nouvelle-fonctionnalite

# Travailler sur votre branche
# ... faire vos modifications ...

# Commit
git add .
git commit -m "feat: Description de la fonctionnalité"

# Push
git push -u origin feature/ma-nouvelle-fonctionnalite
```

#### 3. Mettre à jour votre branche avec main
```bash
# Se placer sur votre branche
git checkout feature/sale-module

# Récupérer les modifications de main
git fetch origin main

# Merger main dans votre branche
git merge origin/main

# Résoudre les conflits si nécessaire
# Push
git push origin feature/sale-module
```

#### 4. Rebase (Alternative au merge)

```bash
# Rebase votre branche sur main
git checkout feature/sale-module
git rebase origin/main

# Si conflits, résoudre puis :
git add .
git rebase --continue

# Push (force nécessaire après rebase)
git push --force-with-lease origin feature/sale-module
```

---

## 📝 Checklist Avant le Merge

Avant de merger votre module, vérifiez :

- [ ] **Tests passent** : `php bin/console cache:clear` fonctionne
- [ ] **Pas de conflits** : `git status` ne montre pas de conflits
- [ ] **Documentation à jour** : Tous les fichiers d'explication sont à jour
- [ ] **Configuration propre** : `.env.example` est à jour
- [ ] **Routes uniques** : Aucune route en conflit avec le module collaborateur
- [ ] **Services uniques** : Aucun service en conflit (namespaces différents)
- [ ] **Paramètres préfixés** : Tous les paramètres utilisent des préfixes (`sale.*`)

---

## 🎯 Scénarios Complets de Merge

### Scénario 1 : Ajouter votre Module dans un Nouveau Repository

**Vous n'êtes pas le propriétaire du repository, vous voulez ajouter votre module.**

```bash
# 1. Cloner le repository du collaborateur
git clone https://github.com/collaborateur/projet.git
cd projet

# 2. Voir les branches existantes
git branch -a

# 3. Se placer sur la branche principale (ou develop)
git checkout main
# OU
git checkout develop

# 4. Récupérer les dernières modifications
git pull origin main

# 5. Créer VOTRE branche pour le module Sale
git checkout -b feature/sale-module

# 6. Copier votre code depuis votre projet ArtEduOrg
# (Copier tous les fichiers sauf .git, node_modules, vendor)

# 7. Ajouter les fichiers
git add .

# 8. Commit
git commit -m "feat: Ajout du module Sale complet

- Système de vente d'œuvres d'art
- Gestion des offres bidirectionnelles
- Intégration Stripe
- API Mailing
- Architecture modulaire avec événements"

# 9. Push votre branche
git push -u origin feature/sale-module

# 10. Sur GitHub/GitLab : Créer Pull Request
# feature/sale-module → main (ou develop)
```

---

### Scénario 2 : Merger votre Branche avec une Autre Branche (Local)

**Vous voulez merger `feature/sale-module` avec `develop` sur votre PC.**

```bash
# 1. Dans le repository cloné
cd projet

# 2. Récupérer toutes les branches
git fetch origin

# 3. Se placer sur la branche cible (develop)
git checkout develop
git pull origin develop

# 4. Merger votre branche
git merge feature/sale-module

# 5. Si conflits, résoudre :
# - Ouvrir les fichiers en conflit
# - Résoudre manuellement
# - Puis :
git add .
git commit -m "Merge: Module Sale intégré dans develop"

# 6. Push
git push origin develop
```

---

### Scénario 3 : Merger via Pull Request (Recommandé)

**Vous créez une PR pour merger votre branche dans une autre branche.**

```bash
# 1. Votre branche est déjà pushée (scénario 1, étape 9)

# 2. Aller sur GitHub/GitLab

# 3. Créer Pull Request :
#    Source : feature/sale-module
#    Destination : develop (ou main, ou autre branche)

# 4. Le collaborateur review et merge via l'interface

# 5. Après merge, mettre à jour localement :
git checkout develop
git pull origin develop
```

---

### Scénario 4 : Merger Deux Branches Différentes

**Vous voulez merger la branche du collaborateur dans la vôtre.**

```bash
# 1. Se placer sur votre branche
git checkout feature/sale-module

# 2. Récupérer la branche du collaborateur
git fetch origin feature/notification-module

# 3. Merger la branche du collaborateur dans la vôtre
git merge origin/feature/notification-module

# 4. Résoudre les conflits si nécessaire
git add .
git commit -m "Merge: Intégration notification-module dans sale-module"

# 5. Push
git push origin feature/sale-module
```

---

### Scénario 5 : Workflow Complet avec Plusieurs Branches

**Le collaborateur a `main`, `develop`, et `feature/notification-module`.**
**Vous voulez ajouter `feature/sale-module` et merger dans `develop`.**

```bash
# ÉTAPE 1 : Cloner et préparer
git clone https://github.com/collaborateur/projet.git
cd projet
git checkout develop
git pull origin develop

# ÉTAPE 2 : Créer votre branche
git checkout -b feature/sale-module

# ÉTAPE 3 : Ajouter votre code
# ... copier vos fichiers ...
git add .
git commit -m "feat: Module Sale"

# ÉTAPE 4 : Push votre branche
git push -u origin feature/sale-module

# ÉTAPE 5 : Sur GitHub, créer PR
# feature/sale-module → develop

# ÉTAPE 6 : Après merge de la PR
git checkout develop
git pull origin develop
# Votre code est maintenant dans develop !

# ÉTAPE 7 : (Optionnel) Merger develop dans main
# C'est généralement fait par le collaborateur
```

---

## 🔧 Commandes Utiles

### Voir les différences
```bash
# Différence entre votre branche et une autre
git diff develop..feature/sale-module

# Différence d'un fichier spécifique
git diff develop..feature/sale-module config/services.yaml

# Différence avec la branche distante
git diff origin/develop..feature/sale-module
```

### Voir les branches
```bash
# Lister toutes les branches locales
git branch

# Lister toutes les branches (locales + distantes)
git branch -a

# Lister uniquement les branches distantes
git branch -r

# Voir sur quelle branche vous êtes
git branch --show-current
```

### Voir l'historique
```bash
# Historique des commits (graphique)
git log --oneline --graph --all

# Historique d'un fichier
git log --follow config/services.yaml

# Historique d'une branche spécifique
git log feature/sale-module --oneline
```

### Récupérer les branches distantes
```bash
# Récupérer toutes les branches distantes (sans merger)
git fetch origin

# Récupérer une branche distante spécifique
git fetch origin develop

# Récupérer et créer une branche locale qui track la distante
git checkout -b develop origin/develop
```

### Annuler des modifications
```bash
# Annuler les modifications non commitées d'un fichier
git checkout -- fichier.php

# Annuler toutes les modifications non commitées
git checkout .

# Annuler le dernier commit (garder les modifications)
git reset --soft HEAD~1

# Annuler le dernier commit (supprimer les modifications)
git reset --hard HEAD~1
```

### Gérer les branches
```bash
# Supprimer une branche locale
git branch -d feature/sale-module

# Forcer la suppression d'une branche locale
git branch -D feature/sale-module

# Supprimer une branche distante
git push origin --delete feature/sale-module

# Renommer une branche locale
git branch -m ancien-nom nouveau-nom
```

### Synchroniser avec le remote
```bash
# Récupérer toutes les modifications distantes
git fetch origin

# Récupérer et merger dans votre branche actuelle
git pull origin develop

# Push votre branche
git push origin feature/sale-module

# Push et créer le tracking
git push -u origin feature/sale-module
```

---

## 🚨 Problèmes Courants et Solutions

### Problème : "Your branch is behind 'origin/main'"

**Solution :**
```bash
git fetch origin
git merge origin/main
# Ou
git pull origin main
```

### Problème : "Merge conflict"

**Solution :**
```bash
# 1. Identifier les fichiers en conflit
git status

# 2. Ouvrir et résoudre manuellement
# 3. Marquer comme résolu
git add fichier-en-conflit.php
git commit -m "Résolution conflit"
```

### Problème : "Permission denied"

**Solution :**
```bash
# Vérifier les permissions SSH
ssh -T git@github.com

# Ou utiliser HTTPS avec token
git remote set-url origin https://token@github.com/user/repo.git
```

---

## 📚 Ressources

- [Documentation Git officielle](https://git-scm.com/doc)
- [GitHub Flow](https://guides.github.com/introduction/flow/)
- [GitLab Flow](https://docs.gitlab.com/ee/topics/gitlab_flow.html)

---

## ✅ Résumé - Workflow Complet

### Pour Ajouter votre Module dans un Repository Existant

1. **Cloner** le repository du collaborateur
2. **Créer une nouvelle branche** : `feature/sale-module`
3. **Copier votre code** dans cette branche
4. **Commit et push** votre branche
5. **Créer Pull Request** : `feature/sale-module` → `develop` (ou autre branche)
6. **Après review** : Merge via l'interface web

### Pour Merger avec une Autre Branche

**Option A : Merge Local (sur votre PC)**
1. Se placer sur la branche cible (`develop`)
2. Merger votre branche : `git merge feature/sale-module`
3. Résoudre les conflits
4. Push : `git push origin develop`

**Option B : Merge via Pull Request (Recommandé)**
1. Créer PR sur GitHub/GitLab
2. Source : `feature/sale-module`
3. Destination : `develop` (ou autre branche)
4. Review et merge via l'interface

### Points Importants

- ✅ **Vous n'êtes pas le main** : Créez toujours une nouvelle branche
- ✅ **Ne jamais push directement sur main** : Utilisez des branches
- ✅ **Pull Request recommandée** : Permet le review avant merge
- ✅ **Résoudre les conflits** : Toujours tester après résolution

**Votre module Sale est maintenant prêt pour la collaboration !** 🎉

