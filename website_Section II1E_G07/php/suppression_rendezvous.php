<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : suppression_rendezvous.php
    Rôle    : Supprimer un rendez-vous via son ID
              Utilise : exec() pour DELETE
*/

require_once 'connexion.php';

$message   = '';
$typeMsg   = '';
$rdvASuppr = null;

if (isset($_GET['id'])) {
    $id   = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM rendezvous WHERE id = ?");
    $stmt->execute([$id]);
    $rdvASuppr = $stmt->fetch();
    if (!$rdvASuppr) { $message = "Aucun rendez-vous trouvé avec l'ID $id."; $typeMsg = 'erreur'; }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_suppr'])) {
    $id = intval($_POST['id_suppr']);
    $nb = $pdo->exec("DELETE FROM rendezvous WHERE id = $id");
    $message   = $nb > 0 ? "✅ Rendez-vous #$id supprimé." : "⚠ ID introuvable.";
    $typeMsg   = $nb > 0 ? 'succes' : 'erreur';
    $rdvASuppr = null;
}

$tous = $pdo->query("SELECT id, nom, prenom, email, date_rdv, heure_rdv, type_consultation FROM rendezvous ORDER BY date_rdv ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Suppression Rendez-vous - QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1000px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #d9534f; }
        .succes { background: #e6f4ea; color: green; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        .erreur { background: #fdecea; color: red; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        .confirm-box { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #d9534f; color: white; padding: 10px; }
        td { padding: 9px; border: 1px solid #ddd; text-align: center; font-size: 13px; }
        tr:nth-child(even) { background: #f9f9f9; }
        .btn-suppr { background: #d9534f; color: white; border: none; padding: 6px 12px; border-radius: 5px; cursor: pointer; text-decoration: none; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-bar input { flex: 1; padding: 9px; border-radius: 5px; border: 1px solid #ccc; }
        .search-bar button { padding: 9px 20px; background: #5cb85c; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .nav-links a { margin-right: 15px; color: #5cb85c; text-decoration: none; font-weight: bold; }
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 12px; }
        .badge-consultation { background: #d4edda; color: #155724; }
        .badge-urgence      { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
<div class="container">
    <div class="nav-links">
        <a href="insertion_rendezvous.php">➕ Ajouter</a>
        <a href="recherche_rendezvous.php">🔍 Rechercher</a>
        <a href="modification_rendezvous.php">✏️ Modifier</a>
        <a href="suppression_rendezvous.php">🗑️ Supprimer</a>
        <a href="../pages/rendezvous.html">← Retour</a>
    </div>
    <h2>🗑️ Supprimer un rendez-vous</h2>
    <?php if ($message): ?><div class="<?= $typeMsg ?>"><?= htmlspecialchars($message) ?></div><?php endif; ?>

    <form method="GET">
        <div class="search-bar">
            <input type="number" name="id" min="1" placeholder="ID du rendez-vous à supprimer" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
            <button type="submit">Rechercher</button>
        </div>
    </form>

    <?php if ($rdvASuppr): ?>
        <div class="confirm-box">
            <p><strong>⚠ Supprimer :</strong> #<?= $rdvASuppr['id'] ?> — <?= htmlspecialchars($rdvASuppr['prenom']) ?> <?= htmlspecialchars($rdvASuppr['nom']) ?></p>
            <p>Email : <?= htmlspecialchars($rdvASuppr['email']) ?> | Date : <?= htmlspecialchars($rdvASuppr['date_rdv']) ?> à <?= htmlspecialchars($rdvASuppr['heure_rdv']) ?></p>
            <form method="POST" style="margin-top:10px;">
                <input type="hidden" name="id_suppr" value="<?= $rdvASuppr['id'] ?>">
                <button type="submit" class="btn-suppr">Confirmer la suppression</button>
                <a href="suppression_rendezvous.php" style="margin-left:15px;">Annuler</a>
            </form>
        </div>
    <?php endif; ?>

    <h3>Tous les rendez-vous :</h3>
    <?php if (empty($tous)): ?>
        <p style="color:gray;">Aucun rendez-vous enregistré.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>#</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Date</th><th>Heure</th><th>Type</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($tous as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><?= htmlspecialchars($r['nom']) ?></td>
                        <td><?= htmlspecialchars($r['prenom']) ?></td>
                        <td><?= htmlspecialchars($r['email']) ?></td>
                        <td><?= htmlspecialchars($r['date_rdv']) ?></td>
                        <td><?= htmlspecialchars($r['heure_rdv']) ?></td>
                        <td><span class="badge badge-<?= $r['type_consultation'] ?>"><?= ucfirst($r['type_consultation']) ?></span></td>
                        <td><a href="suppression_rendezvous.php?id=<?= $r['id'] ?>" class="btn-suppr">Supprimer</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
