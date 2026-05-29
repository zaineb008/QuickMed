-- ============================================================
--  QuickMed – Initialisation de la table : avis
--  Fichier  : avis.sql
--  Membres  : Zeineb Mekki / Israa Trabelsi
-- ============================================================

USE quickmed_db;

INSERT INTO avis (nom, email, satisfait, services, note, commentaire) VALUES
('Ahmed Ben Ali',  'ahmed@email.com', 'oui', 'consultation,rdv',              5, 'Excellent service, très rapide et efficace !'),
('Fatma Riahi',    'fatma@email.com', 'oui', 'teleconsultation',              4, 'Très bonne expérience, interface intuitive.'),
('Mehdi Slama',    'mehdi@email.com', 'non', 'rdv',                           2, 'Temps d attente trop long pour avoir un rendez-vous.'),
('Sonia Belhaj',   'sonia@email.com', 'oui', 'consultation,teleconsultation', 5, 'Je recommande vivement cette plateforme !'),
('Tarek Oueslati', 'tarek@email.com', 'oui', 'consultation,rdv',              4, 'Bonne plateforme, quelques améliorations possibles.');
