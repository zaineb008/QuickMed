-- ============================================================
--  QuickMed – Initialisation de la table : questions
--  Fichier  : questions.sql
--  Membres  : Zeineb Mekki / Israa Trabelsi
-- ============================================================

USE quickmed_db;

INSERT INTO questions (definition, reponse, difficulte, photo) VALUES
('Médecin du cœur',         'cardiologue',   'moyen',     'images/doctor1.jpg'),
('Spécialiste des enfants',  'pediatre',      'facile',    'images/doctor3.jpg'),
('Médecin des yeux',         'ophtalmologue', 'moyen',     'images/doctor4.jpg'),
('Médecin de la peau',       'dermatologue',  'moyen',     'images/doctor2.jpg'),
('Médecin du cerveau',       'neurologue',    'difficile', 'images/doctor7.jpg'),
('Médecin des os',           'orthopediste',  'difficile', 'images/doctor5.jpg'),
('Spécialiste des dents',    'dentiste',      'facile',    'images/logo.jpg');
