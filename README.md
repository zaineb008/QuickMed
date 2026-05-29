# 🏥 QuickMed — Plateforme de Gestion Médicale

> Projet web développé dans le cadre d'un projet universitaire
> Institut Supérieur de Comptabilité et d'Administration des Entreprises (ISCAE)
> Campus Universitaire de Manouba, Tunisie — 2026
> Membres : Zeineb Mekki / Israa Trabelsi

---

## 📌 Description

QuickMed est une plateforme web médicale permettant aux patients de consulter
les profils des médecins, de prendre rendez-vous, de donner leur avis sur les
services, et de jouer à un quiz médical interactif. Le projet couvre à la fois
le développement front-end (HTML, CSS, JavaScript) et back-end (PHP, MySQL).

---

## 🗂️ Structure du Projet
```
QuickMed/
│
├── index.html                          ← Page d'accueil
│
├── css/
│     └── style.css                     ← Feuille de style globale
│
├── images/                             ← Photos médecins, logo
│
├── pages/
│     ├── medecins.html                 ← Liste des médecins
│     ├── details1.html                 ← Profil Dr Ali Ben Salem
│     ├── details2.html                 ← Profil Dr Sana Bouaziz
│     ├── rendezvous.html               ← Formulaire de rendez-vous
│     ├── questionnaire.html            ← Formulaire d'avis
│     ├── funpage.html                  ← Jeu quiz médical
│     ├── about.html                    ← À propos
│     └── contact.html                  ← Contact
│
├── java/
│     ├── details.js                    ← Gestion horaires médecins
│     ├── questionnaire.js              ← Validation + envoi avis
│     └── funpage.js                    ← Logique du jeu quiz
│
└── php/
      ├── connexion.php                 ← Connexion PDO MySQL
      ├── insertion_question.php        ← Ajouter une question
      ├── recherche_questions.php       ← Rechercher des questions
      ├── modification_question.php     ← Modifier une question
      ├── suppression_question.php      ← Supprimer une question
      └── traitement_avis.php           ← Traitement formulaire avis
```
---

## ✨ Fonctionnalités

### 👨‍⚕️ Gestion des Médecins
- Consultation des profils médecins avec photo, spécialité et coordonnées
- Affichage dynamique des horaires de consultation via JavaScript
- Système d'administration protégé par mot de passe (`admin123`)
- Ajout, modification et recherche d'horaires sauvegardés en localStorage

### 📝 Formulaire d'Avis
- Saisie du nom, email, satisfaction, services utilisés, note (étoiles) et commentaire
- Validation complète côté client avec expressions régulières (regex)
- Envoi asynchrone vers le serveur via `fetch()` (sans rechargement de page)
- Calcul et affichage de la moyenne des notes en localStorage

### 🧠 Jeu Quiz Médical
- Deviner des spécialités médicales à partir de définitions
- Chronomètre de 10 secondes par question avec `setInterval`
- Animation de transition entre questions (effet fade CSS)
- Démonstration du concept **Event Bubbling** sur 3 niveaux DOM imbriqués
- Score final et bouton Rejouer

### 🗄️ Panneau d'Administration PHP (CRUD)
- Connexion à la base de données via **PDO**
- Insertion de questions avec requêtes préparées (`prepare` / `execute`)
- Recherche multicritères : par ID (`fetchObject`), par définition (`LIKE`) et par difficulté (`fetchAll`)
- Modification et suppression de questions
- Affichage HTML structuré avec tableau, badges colorés et images miniatures

---

## 🛠️ Technologies Utilisées

| Côté | Technologies |
|------|-------------|
| Front-end | HTML5, CSS3, JavaScript (ES6) |
| Back-end | PHP 8, PDO |
| Base de données | MySQL |
| Stockage client | localStorage |
| Communication async | Fetch API |

---

## 💡 Concepts JavaScript Illustrés

- **Constructeurs d'objets** : `Horaire`, `Question`
- **Manipulation du DOM** : `getElementById`, `insertRow`, `insertCell`
- **Événements** : `addEventListener`, `stopPropagation`, `keydown`
- **Event Bubbling** : propagation sur 3 niveaux imbriqués
- **Timer** : `setInterval`, `clearInterval`, `setTimeout`
- **Regex** : validation email, validation nom
- **Fetch API** : envoi asynchrone POST vers PHP
- **localStorage** : persistance des données côté navigateur

---

## 💡 Concepts PHP Illustrés

- **PDO** : connexion sécurisée à MySQL
- **Requêtes préparées** : protection contre les injections SQL
- **fetch()** : récupération d'une ligne (tableau)
- **fetchObject()** : récupération d'une ligne (objet)
- **fetchAll()** : récupération de toutes les lignes
- **htmlspecialchars()** : protection contre les attaques XSS
- **Requêtes dynamiques** : construction de WHERE avec conditions variables

---

## 🚀 Installation et Lancement

1. Cloner le dépôt :
```bash
   git clone https://github.com/votre-utilisateur/quickmed.git
```

2. Placer le projet dans le dossier de votre serveur local :
   XAMPP  → htdocs/quickmed/
   WAMP   → www/quickmed/
3. Créer la base de données MySQL :
```sql
   CREATE DATABASE quickmed;
   USE quickmed;

   CREATE TABLE questions (
       id         INT AUTO_INCREMENT PRIMARY KEY,
       definition VARCHAR(255) NOT NULL,
       reponse    VARCHAR(100) NOT NULL,
       difficulte ENUM('facile','moyen','difficile') NOT NULL,
       photo      VARCHAR(255)
   );
```

4. Configurer la connexion dans `php/connexion.php` :
```php
   $pdo = new PDO("mysql:host=localhost;dbname=quickmed", "root", "");
```

5. Lancer XAMPP/WAMP et ouvrir dans le navigateur : http://localhost/quickmed/index.html
---

## 👩‍💻 Auteurs

| Nom | Rôle |
|-----|------|
| **Zeineb Mekki** | Développement front-end & back-end |
| **Israa Trabelsi** | Développement front-end & back-end |

---

## 📄 Licence

Projet universitaire — © 2026 QuickMed. Tous droits réservés.
École Nationale des Sciences de l'Informatique
Campus Universitaire de Manouba, Tunisie.

