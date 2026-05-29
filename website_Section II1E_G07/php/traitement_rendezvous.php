<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : traitement_rendezvous.php
    Rôle    : Traitement du formulaire rendezvous.html
              - Valide les données côté serveur PHP
              - Insère dans la table rendezvous (MySQL)
              - Affiche les données reçues + tous les rendez-vous
*/

require_once 'connexion.php';

$erreurs = [];
$succes  = false;
$donnees = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupérer et nettoyer
    $nom              = trim($_POST['nom']    ?? '');
    $prenom           = trim($_POST['prenom'] ?? '');
    $email            = trim($_POST['email']  ?? '');
    $tel              = trim($_POST['tel']    ?? '');
    $date             = trim($_POST['date']   ?? '');
    $heure            = trim($_POST['heure']  ?? '');
    $typeConsultation = trim($_POST['type']   ?? '');
    $sexe             = trim($_POST['sexe']   ?? '');

    // --- Validation PHP ---

    if (!preg_match('/^[a-zA-ZÀ-ÿ\s]{2,}$/', $nom)) {
        $erreurs['nom'] = "Nom invalide (min 2 lettres).";
    }

    if (!preg_match('/^[a-zA-ZÀ-ÿ\s]{2,}$/', $prenom)) {
        $erreurs['prenom'] = "Prénom invalide (min 2 lettres).";
    }

    $regexEmail = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    if (!preg_match($regexEmail, $email)) {
        $erreurs['email'] = "Adresse email invalide.";
    }

    // Téléphone : accepte formats tunisiens (ex: 21671234567, +21671234567, 71234567)
    if (!preg_match('/^\+?[0-9]{8,15}$/', $tel)) {
        $erreurs['tel'] = "Numéro de téléphone invalide.";
    }

    // Date : doit être aujourd'hui ou dans le futur
    if (empty($date) || $date < date('Y-m-d')) {
        $erreurs['date'] = "Date invalide (doit être aujourd'hui ou dans le futur).";
    }

    if (empty($heure)) {
        $erreurs['heure'] = "Veuillez choisir une heure.";
    }

    if (!in_array($typeConsultation, ['consultation', 'urgence'])) {
        $erreurs['type'] = "Type de consultation invalide.";
    }

    if (!in_array($sexe, ['homme', 'femme'])) {
        $erreurs['sexe'] = "Veuillez sélectionner votre sexe.";
    }

    if (empty($erreurs)) {
        // Requête préparée nommée
        $stmt = $pdo->prepare("
            INSERT INTO rendezvous (nom, prenom, email, telephone, date_rdv, heure_rdv, type_consultation, sexe)
            VALUES (:nom, :prenom, :email, :telephone, :date_rdv, :heure_rdv, :type_consultation, :sexe)
        ");
        $stmt->execute([
            ':nom'              => $nom,
            ':prenom'           => $prenom,
            ':email'            => $email,
            ':telephone'        => $tel,
            ':date_rdv'         => $date,
            ':heure_rdv'        => $heure,
            ':type_consultation'=> $typeConsultation,
            ':sexe'             => $sexe,
        ]);

        $succes  = true;
        $donnees = compact('nom', 'prenom', 'email', 'tel', 'date', 'heure', 'typeConsultation', 'sexe');
    }
}

// Récupérer tous les rendez-vous avec fetchAll()
$rdvList = $pdo->query("SELECT * FROM rendezvous ORDER BY date_rdv ASC, heure_rdv ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultat - Rendez-vous QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1000px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; }
        .succes-msg { background: #e6f4ea; color: green; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
        .erreur-msg { background: #fdecea; color: red; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #5cb85c; color: white; padding: 10px; }
        td { padding: 10px; border: 1px solid #ddd; }
        tr:nth-child(even) { background: #f9f9f9; }
        a.btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #5cb85c; color: white; border-radius: 8px; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">

    <h2>📅 Résultat du formulaire Rendez-vous</h2>

    <?php if ($succes): ?>
        <div class="succes-msg">✅ Votre rendez-vous a bien été enregistré !</div>

        <h3>Données reçues :</h3>
        <table>
            <tr><th>Champ</th><th>Valeur</th></tr>
            <tr><td>Nom</td><td><?= htmlspecialchars($donnees['nom']) ?></td></tr>
            <tr><td>Prénom</td><td><?= htmlspecialchars($donnees['prenom']) ?></td></tr>
            <tr><td>Email</td><td><?= htmlspecialchars($donnees['email']) ?></td></tr>
            <tr><td>Téléphone</td><td><?= htmlspecialchars($donnees['tel']) ?></td></tr>
            <tr><td>Date</td><td><?= htmlspecialchars($donnees['date']) ?></td></tr>
            <tr><td>Heure</td><td><?= htmlspecialchars($donnees['heure']) ?></td></tr>
            <tr><td>Type</td><td><?= htmlspecialchars($donnees['typeConsultation']) ?></td></tr>
            <tr><td>Sexe</td><td><?= htmlspecialchars($donnees['sexe']) ?></td></tr>
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

    <h3>Tous les rendez-vous enregistrés :</h3>
    <?php if (empty($rdvList)): ?>
        <p style="color:gray;">Aucun rendez-vous pour le moment.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th><th>Nom</th><th>Prénom</th><th>Email</th>
                    <th>Téléphone</th><th>Date</th><th>Heure</th>
                    <th>Type</th><th>Sexe</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rdvList as $rdv): ?>
                    <tr>
                        <td><?= $rdv['id'] ?></td>
                        <td><?= htmlspecialchars($rdv['nom']) ?></td>
                        <td><?= htmlspecialchars($rdv['prenom']) ?></td>
                        <td><?= htmlspecialchars($rdv['email']) ?></td>
                        <td><?= htmlspecialchars($rdv['telephone']) ?></td>
                        <td><?= htmlspecialchars($rdv['date_rdv']) ?></td>
                        <td><?= htmlspecialchars($rdv['heure_rdv']) ?></td>
                        <td><?= htmlspecialchars($rdv['type_consultation']) ?></td>
                        <td><?= htmlspecialchars($rdv['sexe']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a class="btn" href="../pages/rendezvous.html">← Retour au formulaire</a>
</div>
</body>
</html>
