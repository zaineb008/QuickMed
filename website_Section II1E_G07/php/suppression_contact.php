<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : suppression_contact.php
    Rôle    : Supprimer un message de contact via son ID
              Utilise : exec() pour DELETE
*/

require_once 'connexion.php';

$message  = '';
$typeMsg  = '';
$contact  = null;

if (isset($_GET['id'])) {
    $id   = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = ?");
    $stmt->execute([$id]);
    $contact = $stmt->fetch();
    if (!$contact) { $message = "Aucun message trouvé avec l'ID $id."; $typeMsg = 'erreur'; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_suppr'])) {
    $id = intval($_POST['id_suppr']);
    $nb = $pdo->exec("DELETE FROM contacts WHERE id = $id");  // exec()
    $message = $nb > 0 ? "✅ Message #$id supprimé." : "⚠ ID introuvable.";
    $typeMsg  = $nb > 0 ? 'succes' : 'erreur';
    $contact  = null;
}

$tous = $pdo->query("SELECT id, nom, email, sujet, date_envoi FROM contacts ORDER BY date_envoi DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suppression Contact - QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 900px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #d9534f; }
        .succes { background: #e6f4ea; color: green; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        .erreur { background: #fdecea; color: red; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        .confirm-box { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #d9534f; color: white; padding: 10px; }
        td { padding: 10px; border: 1px solid #ddd; }
        tr:nth-child(even) { background: #f9f9f9; }
        .btn-suppr { background: #d9534f; color: white; border: none; padding: 6px 12px; border-radius: 5px; cursor: pointer; text-decoration: none; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-bar input { flex: 1; padding: 9px; border-radius: 5px; border: 1px solid #ccc; }
        .search-bar button { padding: 9px 20px; background: #2c6ed5; color: white; border: none; border-radius: 5px; cursor: pointer; }
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

    <h2>🗑️ Supprimer un message de contact</h2>

    <?php if ($message): ?><div class="<?= $typeMsg ?>"><?= htmlspecialchars($message) ?></div><?php endif; ?>

    <form method="GET">
        <div class="search-bar">
            <input type="number" name="id" min="1" placeholder="ID du message à supprimer" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
            <button type="submit">Rechercher</button>
        </div>
    </form>

    <?php if ($contact): ?>
        <div class="confirm-box">
            <p><strong>⚠ Supprimer le message de :</strong> <?= htmlspecialchars($contact['nom']) ?> (<?= htmlspecialchars($contact['email']) ?>)</p>
            <p><strong>Sujet :</strong> <?= htmlspecialchars($contact['sujet']) ?></p>
            <p><strong>Message :</strong> <?= htmlspecialchars($contact['message']) ?></p>
            <form method="POST" style="margin-top:10px;">
                <input type="hidden" name="id_suppr" value="<?= $contact['id'] ?>">
                <button type="submit" class="btn-suppr">Confirmer la suppression</button>
                <a href="suppression_contact.php" style="margin-left:15px;">Annuler</a>
            </form>
        </div>
    <?php endif; ?>

    <h3>Tous les messages :</h3>
    <?php if (empty($tous)): ?>
        <p style="color:gray;">Aucun message enregistré.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>#</th><th>Nom</th><th>Email</th><th>Sujet</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($tous as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= htmlspecialchars($c['nom']) ?></td>
                        <td><?= htmlspecialchars($c['email']) ?></td>
                        <td><?= htmlspecialchars($c['sujet']) ?></td>
                        <td><?= htmlspecialchars($c['date_envoi']) ?></td>
                        <td><a href="suppression_contact.php?id=<?= $c['id'] ?>" class="btn-suppr">Supprimer</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
