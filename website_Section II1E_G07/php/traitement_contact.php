<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : traitement_contact.php
    Rôle    : Traitement du formulaire contact.html
              - Valide les données côté serveur PHP
              - Insère dans la table contacts (MySQL)
              - Affiche les données reçues dans un tableau HTML
              - Affiche tous les messages de contact enregistrés
*/

require_once 'connexion.php';

$erreurs = [];
$succes  = false;
$donnees = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupérer et nettoyer
    $nom     = trim($_POST['nom']     ?? '');
    $email   = trim($_POST['email']   ?? '');
    $tel     = trim($_POST['tel']     ?? '');
    $sujet   = trim($_POST['sujet']   ?? '');
    $message = trim($_POST['message'] ?? '');

    // --- Validation PHP ---

    if (strlen($nom) < 3 || !preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $nom)) {
        $erreurs['nom'] = "Nom invalide (min 3 lettres, pas de chiffres).";
    }

    $regexEmail = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    if (!preg_match($regexEmail, $email)) {
        $erreurs['email'] = "Adresse email invalide.";
    }

    // Téléphone : optionnel, mais si rempli doit être valide
    if ($tel !== '' && !preg_match('/^\+?[0-9]{8,15}$/', $tel)) {
        $erreurs['tel'] = "Numéro de téléphone invalide (8 à 15 chiffres).";
    }

    if (strlen($sujet) < 5) {
        $erreurs['sujet'] = "Le sujet doit contenir au moins 5 caractères.";
    }

    if (strlen($message) < 20) {
        $erreurs['message'] = "Le message doit contenir au moins 20 caractères.";
    }

    if (empty($erreurs)) {
        // Requête préparée positionnelle (exec avec ?)
        $stmt = $pdo->prepare("
            INSERT INTO contacts (nom, email, telephone, sujet, message)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$nom, $email, $tel ?: null, $sujet, $message]);

        $succes  = true;
        $donnees = compact('nom', 'email', 'tel', 'sujet', 'message');
    }
}

// Récupérer tous les messages avec fetchAll()
$messages = $pdo->query("SELECT * FROM contacts ORDER BY date_envoi DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultat - Contact QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 900px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; }
        .succes-msg { background: #e6f4ea; color: green; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
        .erreur-msg { background: #fdecea; color: red; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #0066cc; color: white; padding: 10px; }
        td { padding: 10px; border: 1px solid #ddd; }
        tr:nth-child(even) { background: #f9f9f9; }
        a.btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #0066cc; color: white; border-radius: 8px; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">

    <h2>📬 Résultat du formulaire Contact</h2>

    <?php if ($succes): ?>
        <div class="succes-msg">✅ Votre message a bien été envoyé !</div>

        <!-- Afficher les données soumises dans un tableau -->
        <h3>Données reçues :</h3>
        <table>
            <tr><th>Champ</th><th>Valeur</th></tr>
            <tr><td>Nom</td><td><?= htmlspecialchars($donnees['nom']) ?></td></tr>
            <tr><td>Email</td><td><?= htmlspecialchars($donnees['email']) ?></td></tr>
            <tr><td>Téléphone</td><td><?= htmlspecialchars($donnees['tel'] ?: 'Non renseigné') ?></td></tr>
            <tr><td>Sujet</td><td><?= htmlspecialchars($donnees['sujet']) ?></td></tr>
            <tr><td>Message</td><td><?= htmlspecialchars($donnees['message']) ?></td></tr>
        </table>

    <?php elseif (!empty($erreurs) && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="erreur-msg">
            <strong>⚠ Erreurs :</strong>
            <ul>
                <?php foreach ($erreurs as $msg): ?>
                    <li><?= htmlspecialchars($msg) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <h3>Tous les messages reçus :</h3>
    <?php if (empty($messages)): ?>
        <p style="color:gray;">Aucun message pour le moment.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th><th>Nom</th><th>Email</th><th>Téléphone</th>
                    <th>Sujet</th><th>Message</th><th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td><?= $msg['id'] ?></td>
                        <td><?= htmlspecialchars($msg['nom']) ?></td>
                        <td><?= htmlspecialchars($msg['email']) ?></td>
                        <td><?= htmlspecialchars($msg['telephone'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($msg['sujet']) ?></td>
                        <td><?= htmlspecialchars($msg['message']) ?></td>
                        <td><?= htmlspecialchars($msg['date_envoi']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a class="btn" href="../pages/contact.html">← Retour au contact</a>
</div>
</body>
</html>
