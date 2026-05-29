<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : suppression_horaire.php
    Rôle    : Supprimer un horaire via son ID
              Utilise : exec() pour DELETE
*/

require_once 'connexion.php';

$message  = '';
$typeMsg  = '';
$horaire  = null;

if (isset($_GET['id'])) {
    $id   = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM horaires WHERE id = ?");
    $stmt->execute([$id]);
    $horaire = $stmt->fetch();
    if (!$horaire) { $message = "Aucun horaire trouvé avec l'ID $id."; $typeMsg = 'erreur'; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_suppr'])) {
    $id = intval($_POST['id_suppr']);
    $nb = $pdo->exec("DELETE FROM horaires WHERE id = $id");  // exec()
    $message = $nb > 0 ? "✅ Horaire #$id supprimé." : "⚠ ID introuvable.";
    $typeMsg  = $nb > 0 ? 'succes' : 'erreur';
    $horaire  = null;
}

$tous = $pdo->query("SELECT * FROM horaires ORDER BY medecin_nom, jour")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><title>Suppression Horaire - QuickMed</title>
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
        td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        tr:nth-child(even) { background: #f9f9f9; }
        .btn-suppr { background: #d9534f; color: white; border: none; padding: 6px 12px; border-radius: 5px; cursor: pointer; text-decoration: none; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-bar input { flex: 1; padding: 9px; border-radius: 5px; border: 1px solid #ccc; }
        .search-bar button { padding: 9px 20px; background: #2c6ed5; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .nav-links a { margin-right: 15px; color: #5cb85c; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <div class="nav-links">
        <a href="insertion_horaire.php">➕ Ajouter</a>
        <a href="recherche_horaires.php">🔍 Rechercher</a>
        <a href="modification_horaire.php">✏️ Modifier</a>
        <a href="suppression_horaire.php">🗑️ Supprimer</a>
    </div>
    <h2>🗑️ Supprimer un horaire</h2>
    <?php if ($message): ?><div class="<?= $typeMsg ?>"><?= htmlspecialchars($message) ?></div><?php endif; ?>

    <form method="GET">
        <div class="search-bar">
            <input type="number" name="id" min="1" placeholder="ID de l'horaire à supprimer" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
            <button type="submit">Rechercher</button>
        </div>
    </form>

    <?php if ($horaire): ?>
        <div class="confirm-box">
            <p><strong>⚠ Supprimer :</strong> ID #<?= $horaire['id'] ?> — <?= htmlspecialchars($horaire['medecin_nom']) ?> — <?= htmlspecialchars($horaire['jour']) ?> (<?= htmlspecialchars($horaire['matin']) ?> / <?= htmlspecialchars($horaire['apres_midi']) ?>)</p>
            <form method="POST">
                <input type="hidden" name="id_suppr" value="<?= $horaire['id'] ?>">
                <button type="submit" class="btn-suppr">Confirmer la suppression</button>
                <a href="suppression_horaire.php" style="margin-left:15px;">Annuler</a>
            </form>
        </div>
    <?php endif; ?>

    <h3>Tous les horaires :</h3>
    <table>
        <thead><tr><th>#</th><th>Médecin</th><th>Jour</th><th>Matin</th><th>Après-midi</th><th>Action</th></tr></thead>
        <tbody>
            <?php foreach ($tous as $h): ?>
                <tr>
                    <td><?= $h['id'] ?></td>
                    <td><?= htmlspecialchars($h['medecin_nom']) ?></td>
                    <td><?= htmlspecialchars($h['jour']) ?></td>
                    <td><?= htmlspecialchars($h['matin']) ?></td>
                    <td><?= htmlspecialchars($h['apres_midi']) ?></td>
                    <td><a href="suppression_horaire.php?id=<?= $h['id'] ?>" class="btn-suppr">Supprimer</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
