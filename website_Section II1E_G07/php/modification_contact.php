<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : modification_contact.php
    Rôle    : Modifier un message de contact existant (sujet et message)
              Utilise : prepare() + execute() nommé pour UPDATE
*/

require_once 'connexion.php';

$contact = null;
$erreurs = [];
$succes  = false;

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
    $stmt->execute([intval($_GET['id'])]);
    $contact = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id      = intval($_POST['id']      ?? 0);
    $sujet   = trim($_POST['sujet']     ?? '');
    $message = trim($_POST['message']   ?? '');
    $tel     = trim($_POST['telephone'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
    $stmt->execute([$id]);
    $contact = $stmt->fetch();

    if (strlen($sujet) < 5)   $erreurs['sujet']   = "Sujet trop court (min 5 caractères).";
    if (strlen($message) < 20) $erreurs['message'] = "Message trop court (min 20 caractères).";
    if ($tel !== '' && !preg_match('/^\+?[0-9]{8,15}$/', $tel)) $erreurs['tel'] = "Téléphone invalide.";

    if (empty($erreurs)) {
        // prepare() + execute() nommé pour UPDATE
        $stmt = $pdo->prepare("
            UPDATE contacts
            SET sujet = :sujet, message = :message, telephone = :telephone
            WHERE id = :id
        ");
        $stmt->execute([
            ':sujet'     => $sujet,
            ':message'   => $message,
            ':telephone' => $tel ?: null,
            ':id'        => $id,
        ]);
        $succes  = true;
        $contact = array_merge($contact, ['sujet' => $sujet, 'message' => $message, 'telephone' => $tel]);
    }
}

$tous = $pdo->query("SELECT id, nom, sujet FROM contacts ORDER BY date_envoi DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modification Contact - QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .page-wrapper { display: flex; gap: 20px; max-width: 1050px; margin: 30px auto; }
        .sidebar { width: 220px; background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); flex-shrink: 0; }
        .sidebar h4 { color: #0066cc; margin-top: 0; }
        .sidebar a { display: block; padding: 6px 8px; color: #333; text-decoration: none; border-radius: 5px; margin-bottom: 4px; font-size: 13px; }
        .sidebar a:hover { background: #e8f0fe; color: #0066cc; }
        .container { flex: 1; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #f0ad4e; }
        label { font-weight: bold; display: block; margin-top: 12px; }
        input[type="text"], input[type="tel"], textarea {
            width: 100%; padding: 9px; border-radius: 5px; border: 1px solid #ccc; margin-top: 4px; box-sizing: border-box;
        }
        .err { color: red; font-size: 13px; }
        .succes-msg { background: #e6f4ea; color: green; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        button[type="submit"] { margin-top: 20px; padding: 10px 25px; background: #f0ad4e; color: white; border: none; border-radius: 6px; cursor: pointer; width: 100%; }
        .info-readonly { background: #f8f9fa; padding: 10px; border-radius: 6px; margin-bottom: 10px; border: 1px solid #dee2e6; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-bar input { flex: 1; padding: 9px; border-radius: 5px; border: 1px solid #ccc; }
        .search-bar button { padding: 9px 20px; background: #2c6ed5; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .nav-links a { margin-right: 15px; color: #0066cc; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="sidebar">
        <h4>📋 Messages</h4>
        <?php foreach ($tous as $c): ?>
            <a href="modification_contact.php?id=<?= $c['id'] ?>">#<?= $c['id'] ?> – <?= htmlspecialchars($c['nom']) ?></a>
        <?php endforeach; ?>
    </div>

    <div class="container">
        <div class="nav-links">
            <a href="insertion_contact.php">➕ Ajouter</a>
            <a href="recherche_contact.php">🔍 Rechercher</a>
            <a href="modification_contact.php">✏️ Modifier</a>
            <a href="suppression_contact.php">🗑️ Supprimer</a>
            <a href="../pages/contact.html">← Retour</a>
        </div>

        <h2>✏️ Modifier un message de contact</h2>

        <form method="GET">
            <div class="search-bar">
                <input type="number" name="id" min="1" placeholder="ID du message" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
                <button type="submit">Charger</button>
            </div>
        </form>

        <?php if ($succes): ?><div class="succes-msg">✅ Message modifié avec succès !</div><?php endif; ?>

        <?php if ($contact): ?>
            <div class="info-readonly">
                <strong>Nom :</strong> <?= htmlspecialchars($contact['nom']) ?> &nbsp;|&nbsp;
                <strong>Email :</strong> <?= htmlspecialchars($contact['email']) ?> &nbsp;|&nbsp;
                <strong>Date :</strong> <?= htmlspecialchars($contact['date_envoi']) ?>
            </div>

            <form method="POST">
                <input type="hidden" name="id" value="<?= $contact['id'] ?>">

                <label>Téléphone :</label>
                <input type="tel" name="telephone" value="<?= htmlspecialchars($contact['telephone'] ?? '') ?>" placeholder="+21671234567">
                <?php if (isset($erreurs['tel'])): ?><span class="err">⚠ <?= $erreurs['tel'] ?></span><?php endif; ?>

                <label>Sujet :</label>
                <input type="text" name="sujet" value="<?= htmlspecialchars($contact['sujet']) ?>">
                <?php if (isset($erreurs['sujet'])): ?><span class="err">⚠ <?= $erreurs['sujet'] ?></span><?php endif; ?>

                <label>Message :</label>
                <textarea name="message" rows="5"><?= htmlspecialchars($contact['message']) ?></textarea>
                <?php if (isset($erreurs['message'])): ?><span class="err">⚠ <?= $erreurs['message'] ?></span><?php endif; ?>

                <button type="submit">Enregistrer les modifications</button>
            </form>
        <?php elseif (!isset($_GET['id'])): ?>
            <p style="color:gray;">← Sélectionnez un message dans la liste ou entrez un ID.</p>
        <?php else: ?>
            <p style="color:red;">Aucun message trouvé avec cet ID.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
