<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : suppression_question.php
    Rôle    : Supprimer une question via son ID
              Utilise : exec() pour DELETE
*/

require_once 'connexion.php';

$message        = '';
$typeMsg        = '';
$questionASuppr = null;

if (isset($_GET['id'])) {
    $id   = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
    $stmt->execute([$id]);
    $questionASuppr = $stmt->fetch();
    if (!$questionASuppr) { $message = "Aucune question trouvée avec l'ID $id."; $typeMsg = 'erreur'; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_suppr'])) {
    $id = intval($_POST['id_suppr']);
    $nb = $pdo->exec("DELETE FROM questions WHERE id = $id");
    $message = $nb > 0 ? "✅ Question #$id supprimée." : "⚠ ID introuvable.";
    $typeMsg  = $nb > 0 ? 'succes' : 'erreur';
    $questionASuppr = null;
}

$toutes = $pdo->query("SELECT id, definition, difficulte FROM questions ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suppression Question - QuickMed</title>
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
        .search-bar button { padding: 9px 20px; background: #8e44ad; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .nav-links a { margin-right: 15px; color: #8e44ad; text-decoration: none; font-weight: bold; }
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 12px; }
        .badge-facile { background: #e6f4ea; color: green; }
        .badge-moyen { background: #fff3cd; color: #856404; }
        .badge-difficile { background: #fdecea; color: red; }
    </style>
</head>
<body>
<div class="container">
    <div class="nav-links">
        <a href="insertion_question.php">➕ Ajouter</a>
        <a href="recherche_questions.php">🔍 Rechercher</a>
        <a href="modification_question.php">✏️ Modifier</a>
        <a href="suppression_question.php">🗑️ Supprimer</a>
        <a href="../pages/funpage.html">← Retour</a>
    </div>
    <h2>🗑️ Supprimer une question</h2>
    <?php if ($message): ?><div class="<?= $typeMsg ?>"><?= htmlspecialchars($message) ?></div><?php endif; ?>

    <form method="GET">
        <div class="search-bar">
            <input type="number" name="id" min="1" placeholder="ID de la question à supprimer" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
            <button type="submit">Rechercher</button>
        </div>
    </form>

    <?php if ($questionASuppr): ?>
        <div class="confirm-box">
            <p><strong>⚠ Supprimer :</strong> #<?= $questionASuppr['id'] ?> — <?= htmlspecialchars($questionASuppr['definition']) ?></p>
            <p>Réponse : <?= htmlspecialchars($questionASuppr['reponse']) ?> | Difficulté : <?= ucfirst($questionASuppr['difficulte']) ?></p>
            <form method="POST" style="margin-top:10px;">
                <input type="hidden" name="id_suppr" value="<?= $questionASuppr['id'] ?>">
                <button type="submit" class="btn-suppr">Confirmer la suppression</button>
                <a href="suppression_question.php" style="margin-left:15px;">Annuler</a>
            </form>
        </div>
    <?php endif; ?>

    <h3>Toutes les questions :</h3>
    <table>
        <thead><tr><th>#</th><th>Définition</th><th>Difficulté</th><th>Action</th></tr></thead>
        <tbody>
            <?php foreach ($toutes as $q): ?>
                <tr>
                    <td><?= $q['id'] ?></td>
                    <td style="text-align:left;"><?= htmlspecialchars(substr($q['definition'], 0, 80)) ?>...</td>
                    <td><span class="badge badge-<?= $q['difficulte'] ?>"><?= ucfirst($q['difficulte']) ?></span></td>
                    <td><a href="suppression_question.php?id=<?= $q['id'] ?>" class="btn-suppr">Supprimer</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
