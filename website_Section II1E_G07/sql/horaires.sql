-- ============================================================
--  QuickMed – Initialisation de la table : horaires
--  Fichier  : horaires.sql
--  Membres  : Zeineb Mekki / Israa Trabelsi
-- ============================================================

USE quickmed_db;

INSERT INTO horaires (jour, matin, apres_midi, medecin_nom, photo) VALUES
('Lundi',    '8h - 12h', '14h - 18h', 'Dr Ali Ben Salem',  'images/doctor1.jpg'),
('Mardi',    '8h - 12h', '14h - 18h', 'Dr Ali Ben Salem',  'images/doctor1.jpg'),
('Mercredi', '8h - 12h', 'Fermé',     'Dr Ali Ben Salem',  'images/doctor1.jpg'),
('Lundi',    '9h - 13h', '15h - 19h', 'Dr Sara Trabelsi',  'images/doctor2.jpg'),
('Mardi',    '9h - 13h', '15h - 19h', 'Dr Sara Trabelsi',  'images/doctor2.jpg'),
('Lundi',    '8h - 12h', '14h - 17h', 'Dr Mohamed Gharbi', 'images/doctor3.jpg'),
('Jeudi',    '8h - 12h', 'Fermé',     'Dr Mohamed Gharbi', 'images/doctor3.jpg');
