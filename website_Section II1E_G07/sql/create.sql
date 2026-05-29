-- ============================================================
--  QuickMed – Création de la base de données et des tables
--  Fichier  : create.sql
--  Membres  : Zeineb Mekki / Israa Trabelsi
-- ============================================================

CREATE DATABASE IF NOT EXISTS quickmed_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE quickmed_db;

-- TABLE 1 : questions
-- Objet JS : function Question(definition, reponse, difficulte, photo)
CREATE TABLE IF NOT EXISTS questions (
    id          INT           AUTO_INCREMENT PRIMARY KEY,
    definition  VARCHAR(255)  NOT NULL,
    reponse     VARCHAR(100)  NOT NULL,
    difficulte  ENUM('facile','moyen','difficile') NOT NULL DEFAULT 'facile',
    photo       VARCHAR(255)  NOT NULL
);

-- TABLE 2 : horaires
-- Objet JS : function Horaire(jour, matin, apresMidi, medecinNom, photo)
CREATE TABLE IF NOT EXISTS horaires (
    id          INT           AUTO_INCREMENT PRIMARY KEY,
    jour        VARCHAR(20)   NOT NULL,
    matin       VARCHAR(50)   NOT NULL,
    apres_midi  VARCHAR(50)   NOT NULL,
    medecin_nom VARCHAR(100)  NOT NULL,
    photo       VARCHAR(255)  NOT NULL
);

-- TABLE 3 : avis
-- Objet JS : let avis = { nom, email, satisfait, services, note, commentaire }
CREATE TABLE IF NOT EXISTS avis (
    id          INT           AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL,
    satisfait   ENUM('oui','non') NOT NULL,
    services    VARCHAR(255)  NOT NULL,
    note        TINYINT       NOT NULL CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT          NOT NULL,
    date_avis   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- TABLE 4 : contacts
-- Reçoit les soumissions du formulaire contact.html
CREATE TABLE IF NOT EXISTS contacts (
    id          INT           AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL,
    telephone   VARCHAR(20)   DEFAULT NULL,
    sujet       VARCHAR(100)  NOT NULL,
    message     TEXT          NOT NULL,
    date_envoi  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- TABLE 5 : rendezvous
-- Reçoit les soumissions du formulaire rendezvous.html
CREATE TABLE IF NOT EXISTS rendezvous (
    id                INT           AUTO_INCREMENT PRIMARY KEY,
    nom               VARCHAR(100)  NOT NULL,
    prenom            VARCHAR(100)  NOT NULL,
    email             VARCHAR(150)  NOT NULL,
    telephone         VARCHAR(20)   NOT NULL,
    date_rdv          DATE          NOT NULL,
    heure_rdv         TIME          NOT NULL,
    type_consultation ENUM('consultation','urgence') NOT NULL,
    sexe              ENUM('homme','femme') NOT NULL,
    date_creation     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
);
