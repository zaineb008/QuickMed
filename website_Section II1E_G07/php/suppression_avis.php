<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : suppression_avis.php
    Rôle    : Supprimer un avis via son ID unique
              Utilise : exec() pour la suppression directe
                        prepare() + execute() positionnel pour la recherche avant suppression
*/

require_once 'connexion.php';

$message    = '';
$typeMsg    = '';
$avisASuppr = null;

// Étape 1 : Chercher l'avis par ID avant de supprimer (confirmation)
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM avis WHERE id = ?");  // prepare() + positionnel
    $stmt->execute([$id]);
    $avisASuppr = $stmt->fetch();

    if (!$avisASuppr) {
        $message = "Aucun avis trouvé avec l'ID $id.";
        $typeMsg = 'erreur';
    }
}

// Étape 2 : Confirmer et supprimer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_suppr'])) {
    $id = intval($_POST['id_suppr']);

    if ($id > 0) {
        // exec() → utilisé pour DELETE simple sans retour de données
        $nb = $pdo->exec("DELETE FROM avis WHERE id = $id");

        if ($nb > 0) {
            $message = "✅ L'avis #$id a été supprimé avec succès.";
            $typeMsg = 'succes';
            $avisASuppr = null;
        } else {
            $message = "⚠ Aucun avis trouvé avec l'ID $id.";
            $typeMsg = 'erreur';
        }
    }
}

// Récupérer tous les avis pour affichage
$tousLesAvis = $pdo->query("SELECT id, nom, email, note, satisfait, date_avis FROM avis ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suppression Avis - QuickMed</title>
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
        .btn-suppr:hover { background: #c9302c; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-bar input { flex: 1; padding: 9px; border-radius: 5px; border: 1px solid #ccc; }
        .search-bar button { padding: 9px 20px; background: #2c6ed5; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .nav-links a { margin-right: 15px; color: #2c6ed5; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">

    <div class="nav-links">
        <a href="insertion_avis.php">➕ Ajouter</a>
        <a href="recherche_avis.php">🔍 Rechercher</a>
        <a href="modification_avis.php">✏️ Modifier</a>
        <a href="suppression_avis.php">🗑️ Supprimer</a>
        <a href="../pages/questionnaire.html">← Retour</a>
    </div>

    <h2>🗑️ Supprimer un avis</h2>

    <?php if ($message): ?>
        <div class="<?= $typeMsg ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Recherche par ID -->
    <form method="GET">
        <div class="search-bar">
            <input type="number" name="id" min="1" placeholder="Entrez l'ID de l'avis à supprimer" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
            <button type="submit">Rechercher</button>
        </div>
    </form>

    <!-- Confirmation avant suppression -->
    <?php if ($avisASuppr): ?>
        <div class="confirm-box">
            <p><strong>⚠ Vous allez supprimer l'avis suivant :</strong></p>
            <p>ID : <strong><?= $avisASuppr['id'] ?></strong> | Nom : <strong><?= htmlspecialchars($avisASuppr['nom']) ?></strong> | Email : <?= htmlspecialchars($avisASuppr['email']) ?></p>
            <p>Note : <?= str_repeat('★', $avisASuppr['note']) ?> | Commentaire : <?= htmlspecialchars($avisASuppr['commentaire']) ?></p>
            <form method="POST" style="margin-top:10px;">
                <input type="hidden" name="id_suppr" value="<?= $avisASuppr['id'] ?>">
                <button type="submit" class="btn-suppr">Confirmer la suppression</button>
                <a href="suppression_avis.php" style="margin-left:15px; color:#555;">Annuler</a>
            </form>
        </div>
    <?php endif; ?>

    <!-- Liste de tous les avis avec bouton supprimer -->
    <h3>Tous les avis :</h3>
    <?php if (empty($tousLesAvis)): ?>
        <p style="color:gray;">Aucun avis enregistré.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>#</th><th>Nom</th><th>Email</th><th>Note</th><th>Satisfait</th><th>Date</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach ($tousLesAvis as $a): ?>
                    <tr>
                        <td><?= $a['id'] ?></td>
                        <td><?= htmlspecialchars($a['nom']) ?></td>
                        <td><?= htmlspecialchars($a['email']) ?></td>
                        <td style="color:gold;"><?= str_repeat('★', $a['note']) ?></td>
                        <td><?= $a['satisfait'] === 'oui' ? '✅' : '❌' ?></td>
                        <td><?= htmlspecialchars($a['date_avis']) ?></td>
                        <td><a href="suppression_avis.php?id=<?= $a['id'] ?>" class="btn-suppr">Supprimer</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>
</body>
</html>
