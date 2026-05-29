<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : insertion_contact.php
    Rôle    : Ajouter un message de contact manuellement
              Utilise : prepare() + execute() nommé
*/

require_once 'connexion.php';

$erreurs = [];
$succes  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom     = trim($_POST['nom']     ?? '');
    $email   = trim($_POST['email']   ?? '');
    $tel     = trim($_POST['tel']     ?? '');
    $sujet   = trim($_POST['sujet']   ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validation PHP
    if (strlen($nom) < 3 || !preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $nom))
        $erreurs['nom'] = "Nom invalide (min 3 lettres).";
    $regexEmail = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    if (!preg_match($regexEmail, $email))
        $erreurs['email'] = "Email invalide.";
    if ($tel !== '' && !preg_match('/^\+?[0-9]{8,15}$/', $tel))
        $erreurs['tel'] = "Téléphone invalide.";
    if (strlen($sujet) < 5)
        $erreurs['sujet'] = "Sujet trop court (min 5 caractères).";
    if (strlen($message) < 20)
        $erreurs['message'] = "Message trop court (min 20 caractères).";

    if (empty($erreurs)) {
        // prepare() + execute() nommé
        $stmt = $pdo->prepare("
            INSERT INTO contacts (nom, email, telephone, sujet, message)
            VALUES (:nom, :email, :telephone, :sujet, :message)
        ");
        $stmt->execute([
            ':nom'       => $nom,
            ':email'     => $email,
            ':telephone' => $tel ?: null,
            ':sujet'     => $sujet,
            ':message'   => $message,
        ]);
        $succes = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Insertion Contact - QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 650px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #0066cc; }
        label { font-weight: bold; display: block; margin-top: 12px; }
        input[type="text"], input[type="email"], input[type="tel"], textarea {
            width: 100%; padding: 9px; border-radius: 5px; border: 1px solid #ccc; margin-top: 4px; box-sizing: border-box;
        }
        .err { color: red; font-size: 13px; }
        .succes-msg { background: #e6f4ea; color: green; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        button[type="submit"] { margin-top: 20px; padding: 10px 25px; background: #0066cc; color: white; border: none; border-radius: 6px; cursor: pointer; width: 100%; }
        button[type="submit"]:hover { background: #004999; }
        .nav-links a { margin-right: 15px; color: #0066cc; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <div class="nav-links">
        <a href="insertion_contact.php">➕ Ajouter</a>
        <a href="recherche_contact.php">🔍 Rechercher</a>
        <a href="modification_contact.php">✏️ Modifier</a>
        <a href="suppression_contact.php">🗑️ Supprimer</a>
        <a href="../pages/contact.html">← Retour</a>
    </div>

    <h2>➕ Ajouter un message de contact</h2>

    <?php if ($succes): ?>
        <div class="succes-msg">✅ Message ajouté ! <a href="recherche_contact.php">Voir tous</a></div>
    <?php endif; ?>

    <form method="POST">
        <label>Nom :</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" placeholder="Ex: Ahmed Ben Ali">
        <?php if (isset($erreurs['nom'])): ?><span class="err">⚠ <?= $erreurs['nom'] ?></span><?php endif; ?>

        <label>Email :</label>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="Ex: ahmed@email.com">
        <?php if (isset($erreurs['email'])): ?><span class="err">⚠ <?= $erreurs['email'] ?></span><?php endif; ?>

        <label>Téléphone (optionnel) :</label>
        <input type="tel" name="tel" value="<?= htmlspecialchars($_POST['tel'] ?? '') ?>" placeholder="Ex: +21671234567">
        <?php if (isset($erreurs['tel'])): ?><span class="err">⚠ <?= $erreurs['tel'] ?></span><?php endif; ?>

        <label>Sujet :</label>
        <input type="text" name="sujet" value="<?= htmlspecialchars($_POST['sujet'] ?? '') ?>" placeholder="Ex: Demande d'information">
        <?php if (isset($erreurs['sujet'])): ?><span class="err">⚠ <?= $erreurs['sujet'] ?></span><?php endif; ?>

        <label>Message :</label>
        <textarea name="message" rows="5" placeholder="Votre message (min 20 caractères)..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
        <?php if (isset($erreurs['message'])): ?><span class="err">⚠ <?= $erreurs['message'] ?></span><?php endif; ?>

        <button type="submit">Enregistrer</button>
    </form>
</div>
</body>
</html>
