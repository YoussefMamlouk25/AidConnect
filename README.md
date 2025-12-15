# 🎯 AidConnect - Plateforme de Solidarité et d'Entraide

<div align="center">

![AidConnect Logo](View/frontoffice/assets/images/logo.png)

**Une plateforme web complète pour connecter les personnes dans le besoin avec des bénévoles et des donateurs**

[![PHP Version](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-10.4+-orange.svg)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

</div>

---

## 📋 Table des matières

- [À propos](#-à-propos)
- [Fonctionnalités](#-fonctionnalités)
- [Technologies utilisées](#-technologies-utilisées)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Structure du projet](#-structure-du-projet)
- [Utilisation](#-utilisation)
- [Base de données](#-base-de-données)
- [API et Endpoints](#-api-et-endpoints)
- [Dashboard Administrateur](#-dashboard-administrateur)
- [Sécurité](#-sécurité)
- [Dépannage](#-dépannage)
- [Contribution](#-contribution)
- [Licence](#-licence)

---

## 🎯 À propos

**AidConnect** est une plateforme web de solidarité qui permet de :

- 📝 **Créer des posts** pour exprimer des besoins (logement, nourriture, équipement, etc.)
- 🎁 **Publier des offres** de dons et de services
- 📅 **Organiser des événements** caritatifs (collectes, distributions, soutien)
- 👥 **Connecter les utilisateurs** entre ceux qui ont besoin d'aide et ceux qui peuvent aider
- 💬 **Interagir** via des likes, commentaires et partages
- 📊 **Gérer** via un dashboard administrateur complet avec statistiques détaillées

---

## ✨ Fonctionnalités

### 👤 Gestion des Utilisateurs

- ✅ **Inscription** avec vérification email
- ✅ **Connexion/Déconnexion** sécurisée
- ✅ **Profils utilisateurs** avec photos de profil
- ✅ **Rôles** : Admin et Volunteer
- ✅ **Gestion de profil** (édition, changement de mot de passe)
- ✅ **Vérification email** avec code à 6 chiffres

### 📝 Système de Posts

- ✅ **Création de posts** avec titre, description, région, médias
- ✅ **Affichage** de tous les posts avec pagination
- ✅ **Recherche** et filtrage par titre, région, date
- ✅ **Likes** et **Commentaires** sur les posts
- ✅ **Partage** de posts
- ✅ **Mise à jour/Résolution** de posts (marquer comme résolu)
- ✅ **Suppression** de posts (auteur ou admin)
- ✅ **Impression** de posts

### 🎁 Système d'Offres

- ✅ **Création d'offres** de dons (nourriture, équipement, vêtements, etc.)
- ✅ **Types de dons** : food, equipment, clothing, money, services
- ✅ **Médias** : upload d'images multiples
- ✅ **Likes** sur les offres
- ✅ **Mises à jour** d'offres (suivi de l'état du don)
- ✅ **Recherche avancée** avec filtres multiples
- ✅ **Gestion complète** (création, édition, suppression)

### 📅 Système d'Événements

- ✅ **Création d'événements** caritatifs
- ✅ **Types d'événements** : collecte, distribution, soutien, logement, autre
- ✅ **Inscriptions** aux événements
- ✅ **Gestion des places** disponibles
- ✅ **Statuts** : en attente, confirmé, annulé
- ✅ **Avis et notes** sur les événements (1-5 étoiles)
- ✅ **Images** pour les événements

### 📊 Dashboard Administrateur

- ✅ **Statistiques globales** : Posts, Offres, Utilisateurs, Événements
- ✅ **Graphiques en courbes** : Tendances temporelles (6 derniers mois)
- ✅ **Graphiques circulaires** : Répartitions par région, type, rôle
- ✅ **Pourcentages** affichés dans tous les graphiques
- ✅ **Statistiques détaillées** :
  - Posts par région
  - Offres par type de don
  - Utilisateurs par rôle et région
  - Taux de croissance mensuels
- ✅ **Gestion complète** de tous les contenus
- ✅ **Gestion des utilisateurs** (création, édition, suppression)

### 🔍 Recherche et Filtres

- ✅ **Recherche globale** dans posts et offres
- ✅ **Filtres avancés** :
  - Par région
  - Par type de don
  - Par date
  - Par statut (résolu/non résolu)
  - Par présence de médias

### 📧 Système d'Email

- ✅ **Vérification email** lors de l'inscription
- ✅ **Envoi d'emails** via PHPMailer (SMTP)
- ✅ **Support Gmail, Outlook, Yahoo**
- ✅ **Codes de vérification** avec expiration (15 minutes)

---

## 🛠 Technologies utilisées

### Backend
- **PHP 8.2+** - Langage de programmation
- **MySQL/MariaDB 10.4+** - Base de données
- **PDO** - Accès aux données
- **PHPMailer** - Envoi d'emails

### Frontend
- **HTML5** - Structure
- **CSS3** - Styles (Bootstrap, Custom CSS)
- **JavaScript** - Interactivité
- **Chart.js 4.4.0** - Graphiques et statistiques
- **RemixIcon** - Icônes
- **Bootstrap 5** - Framework CSS

### Outils et Bibliothèques
- **jQuery** - Manipulation DOM
- **Dropzone.js** - Upload de fichiers
- **SweetAlert2** - Alertes modernes
- **Select2** - Sélecteurs améliorés
- **Owl Carousel** - Carrousels
- **Masonry** - Layout en grille

---

## 📦 Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- **XAMPP** (ou WAMP/MAMP) avec :
  - PHP 8.2 ou supérieur
  - MySQL/MariaDB 10.4 ou supérieur
  - Apache Server
- **Composer** (optionnel, pour PHPMailer)
- **Un navigateur web moderne** (Chrome, Firefox, Edge, Safari)

---

## 🚀 Installation

### Étape 1 : Cloner ou télécharger le projet

```bash
# Si vous utilisez Git
git clone <repository-url>
cd aidconnect!!

# Ou téléchargez et extrayez l'archive ZIP
```

### Étape 2 : Placer le projet dans XAMPP

```bash
# Copiez le dossier 'aidconnect!!' dans :
C:\xampp\htdocs\aidconnect!!

# Ou pour Linux/Mac :
/opt/lampp/htdocs/aidconnect!!/
```

### Étape 3 : Créer la base de données

1. Ouvrez **phpMyAdmin** : `http://localhost/phpmyadmin`
2. Créez une nouvelle base de données nommée `aidconnect`
3. Importez le fichier SQL : `aidconnect (2).sql`

```sql
-- Ou exécutez manuellement :
CREATE DATABASE aidconnect CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE aidconnect;
-- Puis importez le fichier SQL
```

### Étape 4 : Configurer la connexion à la base de données

Éditez le fichier `config.php` :

```php
// aidconnect!!/config.php
$servername = "localhost";
$username = "root";        // Votre nom d'utilisateur MySQL
$password = "";            // Votre mot de passe MySQL
$dbname = "aidconnect";   // Nom de la base de données
```

### Étape 5 : Configurer l'envoi d'emails (optionnel)

1. Copiez `Model/EmailConfig.example.php` vers `Model/EmailConfig.php`
2. Éditez `Model/EmailConfig.php` avec vos paramètres SMTP :

```php
'from_email' => 'votre-email@gmail.com',
'from_name' => 'AidConnect',
'method' => 'smtp',
'smtp' => [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'secure' => 'tls',
    'username' => 'votre-email@gmail.com',
    'password' => 'votre-mot-de-passe-application', // Mot de passe d'application Gmail
],
```

**Note** : Pour Gmail, vous devez créer un [mot de passe d'application](https://support.google.com/accounts/answer/185833).

### Étape 6 : Configurer les permissions

Assurez-vous que le dossier `public/uploads/` est accessible en écriture :

```bash
# Linux/Mac
chmod -R 755 public/uploads/
chmod -R 755 public/uploads/profile/
chmod -R 755 public/uploads/events/

# Windows : Clic droit > Propriétés > Sécurité > Modifier les permissions
```

### Étape 7 : Démarrer les services

1. Démarrez **XAMPP Control Panel**
2. Démarrez **Apache** et **MySQL**
3. Ouvrez votre navigateur : `http://localhost/aidconnect!!/View/frontoffice/`

---

## ⚙️ Configuration

### Configuration de la base de données

Fichier : `config.php`

```php
class Config {
    public static function getConnexion() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "aidconnect";
        // ...
    }
}
```

### Configuration des emails

Fichier : `Model/EmailConfig.php`

```php
return [
    'from_email' => 'votre-email@gmail.com',
    'from_name' => 'AidConnect',
    'method' => 'smtp', // ou 'mail'
    'smtp' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'secure' => 'tls',
        'username' => 'votre-email@gmail.com',
        'password' => 'votre-mot-de-passe',
    ],
];
```

### Configuration PHP

Assurez-vous que les extensions suivantes sont activées dans `php.ini` :

```ini
extension=pdo_mysql
extension=gd
extension=mbstring
extension=openssl
```

---

## 📁 Structure du projet

```
aidconnect!!/
│
├── config.php                 # Configuration de la base de données
│
├── Controller/                # Contrôleurs (logique métier)
│   ├── postcontroller.php
│   ├── offercontroller.php
│   ├── eventcontroller.php
│   ├── userController.php
│   ├── logincontroller.php
│   ├── signupcontroller.php
│   ├── postlikecontroller.php
│   ├── postcommentcontroller.php
│   └── ...
│
├── Model/                      # Modèles (accès aux données)
│   ├── user.php
│   ├── post.php
│   ├── offer.php
│   ├── Event.php
│   ├── Inscription.php
│   ├── EmailService.php
│   └── EmailConfig.php
│
├── View/                       # Vues (interface utilisateur)
│   ├── frontoffice/           # Interface publique
│   │   ├── index.php          # Page d'accueil
│   │   ├── login.php          # Connexion
│   │   ├── signup.php         # Inscription
│   │   ├── showPosts.php      # Liste des posts
│   │   ├── addpost.php        # Créer un post
│   │   ├── volunteer-view.php # Liste des offres
│   │   ├── volunteer-form.php # Créer une offre
│   │   ├── event.php          # Liste des événements
│   │   ├── profile.php        # Profil utilisateur
│   │   ├── search.php         # Recherche
│   │   └── assets/            # CSS, JS, Images
│   │
│   └── backoffice/            # Interface administrateur
│       ├── dashboard.php      # Dashboard avec statistiques
│       ├── users.php          # Gestion des utilisateurs
│       ├── showpostback.php   # Gestion des posts
│       ├── showoffer.php      # Gestion des offres
│       ├── showevent.php      # Gestion des événements
│       └── ...
│
├── public/                     # Fichiers publics
│   └── uploads/               # Uploads utilisateurs
│       ├── profile/           # Photos de profil
│       └── events/            # Images d'événements
│
└── PHPMailer-master/          # Bibliothèque PHPMailer
```

---

## 💻 Utilisation

### Pour les Utilisateurs

#### Inscription
1. Accédez à `http://localhost/aidconnect!!/View/frontoffice/signup.php`
2. Remplissez le formulaire d'inscription
3. Vérifiez votre email avec le code reçu
4. Connectez-vous avec vos identifiants

#### Créer un Post
1. Connectez-vous
2. Cliquez sur "Ajouter un Post"
3. Remplissez les informations (titre, description, région, médias)
4. Publiez votre post

#### Créer une Offre
1. Connectez-vous
2. Allez dans "Voir les Offres" > "Créer une Offre"
3. Sélectionnez le type de don
4. Ajoutez une description et des images
5. Publiez votre offre

#### S'inscrire à un Événement
1. Parcourez les événements disponibles
2. Cliquez sur un événement
3. Remplissez le formulaire d'inscription
4. Attendez la confirmation

### Pour les Administrateurs

#### Accéder au Dashboard
1. Connectez-vous avec un compte admin
2. Accédez à `http://localhost/aidconnect!!/View/backoffice/dashboard.php`
3. Visualisez les statistiques et graphiques

#### Gérer les Utilisateurs
- Menu : **Utilisateurs**
- Actions : Voir, Modifier, Supprimer

#### Gérer les Posts
- Menu : **Posts** ou **Posts Résolus**
- Actions : Voir, Modifier, Supprimer, Voir les likes/comments

#### Gérer les Offres
- Menu : **Offres** ou **Updates Offres**
- Actions : Voir, Modifier, Supprimer

#### Gérer les Événements
- Menu : **Événements**
- Actions : Créer, Modifier, Supprimer, Voir les inscriptions

---

## 🗄 Base de données

### Tables principales

#### `user`
- Gestion des utilisateurs
- Champs : id_user, name, email, password_hash, role, region, profile_pic, etc.

#### `post`
- Posts des utilisateurs
- Champs : id_post, id_user, titre, description, region, date_creation, media, etc.

#### `offer`
- Offres de dons
- Champs : id_offer, id_user, description, type_don, media, nom, email, num, etc.

#### `events`
- Événements caritatifs
- Champs : id_event, titre, description, date_event, lieu, nombre_places, type_event, etc.

#### `inscriptions`
- Inscriptions aux événements
- Champs : id_inscription, id_event, nom, prenom, email, statut, note, etc.

#### `post_like` / `offer_like`
- Likes sur posts et offres

#### `post_comment`
- Commentaires sur les posts

#### `postupdate` / `update_offer`
- Mises à jour des posts et offres

### Diagramme de relations

```
user (1) ────< (N) post
user (1) ────< (N) offer
user (1) ────< (N) inscriptions
events (1) ────< (N) inscriptions
post (1) ────< (N) post_like
post (1) ────< (N) post_comment
post (1) ────< (N) postupdate
offer (1) ────< (N) offer_like
offer (1) ────< (N) update_offer
```

---

## 🔌 API et Endpoints

### Contrôleurs disponibles

#### PostController
- `getAllPosts()` - Récupérer tous les posts
- `getPostById($id)` - Récupérer un post par ID
- `addPost(Post $post, $id_user)` - Ajouter un post
- `updatePost(Post $post, $id, $ownerId)` - Modifier un post
- `deletePost($id, $ownerId)` - Supprimer un post
- `searchPosts($query, $filters)` - Rechercher des posts

#### OfferController
- `getAllOffers()` - Récupérer toutes les offres
- `getOfferById($id)` - Récupérer une offre par ID
- `addOffer(Offer $offer)` - Ajouter une offre
- `updateOffer(Offer $offer, $id, $ownerId)` - Modifier une offre
- `deleteOffer($id, $ownerId)` - Supprimer une offre
- `searchOffers($query, $filters)` - Rechercher des offres

#### UserController
- `getAllUsers()` - Récupérer tous les utilisateurs
- `getUserById($id)` - Récupérer un utilisateur par ID
- `updateUserByAdmin($id, ...)` - Modifier un utilisateur (admin)
- `deleteUser($id)` - Supprimer un utilisateur

#### EventController
- `liste()` - Liste des événements
- `detail()` - Détails d'un événement
- `create()` - Créer un événement
- `update()` - Modifier un événement
- `delete()` - Supprimer un événement

---

## 📊 Dashboard Administrateur

### Statistiques disponibles

#### Cartes principales
- **Posts** : Total, likes, commentaires
- **Offres** : Total, likes
- **Utilisateurs** : Total, admins, volunteers
- **Événements** : Total d'événements actifs

#### Graphiques en courbes
- **Tendances temporelles** : Évolution sur 6 mois
  - Posts par mois
  - Offres par mois
  - Utilisateurs par mois
  - Pourcentages de croissance

#### Graphiques circulaires
- **Posts par région** (Doughnut chart)
- **Offres par type de don** (Pie chart)
- **Utilisateurs par rôle** (Doughnut chart)
- **Utilisateurs par région** (Pie chart)

#### Statistiques détaillées
- Posts avec/sans média
- Likes et commentaires moyens
- Types de dons différents
- Taux de croissance mensuels

---

## 🔒 Sécurité

### Mesures de sécurité implémentées

- ✅ **Hachage des mots de passe** : Bcrypt
- ✅ **Protection CSRF** : Tokens de session
- ✅ **Validation des entrées** : Filtrage et validation
- ✅ **Prévention SQL Injection** : Requêtes préparées (PDO)
- ✅ **Échappement HTML** : `htmlspecialchars()`
- ✅ **Gestion des sessions** : Sécurisation des sessions
- ✅ **Vérification des permissions** : Contrôle d'accès par rôle
- ✅ **Upload sécurisé** : Validation des types de fichiers
- ✅ **Vérification email** : Codes à 6 chiffres avec expiration

### Recommandations

1. **Changez les mots de passe par défaut** de la base de données
2. **Activez HTTPS** en production
3. **Limitez les permissions** des fichiers uploadés
4. **Configurez un firewall** pour protéger le serveur
5. **Faites des sauvegardes régulières** de la base de données

---

## 🐛 Dépannage

### Problèmes courants

#### Erreur de connexion à la base de données
```
Solution : Vérifiez config.php et assurez-vous que MySQL est démarré
```

#### Les images ne s'affichent pas
```
Solution : Vérifiez les permissions du dossier public/uploads/
```

#### Les emails ne sont pas envoyés
```
Solution : 
1. Vérifiez EmailConfig.php
2. Pour Gmail, utilisez un mot de passe d'application
3. Vérifiez que PHPMailer est correctement installé
```

#### Erreur 404 sur les pages
```
Solution : Vérifiez que mod_rewrite est activé dans Apache
```

#### Les graphiques ne s'affichent pas
```
Solution : Vérifiez que Chart.js est chargé (CDN) et que JavaScript est activé
```

#### Problème d'upload de fichiers
```
Solution :
1. Vérifiez upload_max_filesize dans php.ini
2. Vérifiez les permissions du dossier uploads/
3. Vérifiez post_max_size dans php.ini
```

---

## 🤝 Contribution

Les contributions sont les bienvenues ! Pour contribuer :

1. **Fork** le projet
2. **Créez** une branche pour votre fonctionnalité (`git checkout -b feature/AmazingFeature`)
3. **Commitez** vos changements (`git commit -m 'Add some AmazingFeature'`)
4. **Push** vers la branche (`git push origin feature/AmazingFeature`)
5. **Ouvrez** une Pull Request

### Standards de code

- Suivez les conventions PSR-12 pour PHP
- Commentez votre code
- Testez vos modifications
- Mettez à jour la documentation si nécessaire

---

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

---

## 👥 Auteurs

- **Équipe AidConnect** - Développement initial

---

## 🙏 Remerciements

- **Bootstrap** - Framework CSS
- **Chart.js** - Bibliothèque de graphiques
- **PHPMailer** - Envoi d'emails
- **RemixIcon** - Icônes
- **Tous les contributeurs** qui ont aidé à améliorer ce projet

---

## 📞 Support

Pour toute question ou problème :

- 📧 Email : youssefmamlouk72@gmail.com
- 🐛 Issues : [GitHub Issues](https://github.com/aidconnect/issues)
- 📖 Documentation : Voir ce README

---

<div align="center">

**Fait avec ❤️ pour aider la communauté**

⭐ Si ce projet vous a aidé, n'hésitez pas à lui donner une étoile !

</div>

