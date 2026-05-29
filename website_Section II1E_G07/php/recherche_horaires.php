<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : recherche_horaires.php
    Rôle    : Recherche dans la table horaires
              Utilise : query(), fetch(), fetchAll(), fetchObject()
*/

require_once 'connexion.php';

$resultats     = [];
$resultatObjet = null;
$recherche     = false;

// Nombre total via query() + fetch()
$total = $pdo->query("SELECT COUNT(*) AS total FROM horaires")->fetch()['total'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recherche   = true;
    $medecin     = trim($_POST['medecin']  ?? '');
    $jour        = trim($_POST['jour']     ?? '');
    $id_exact    = intval($_POST['id_exact'] ?? 0);

    // fetchObject() : recherche par ID exact
    if ($id_exact > 0) {
        $stmt = $pdo->prepare("SELECT * FROM horaires WHERE id = :id");
        $stmt->execute([':id' => $id_exact]);
        $resultatObjet = $stmt->fetchObject();
    }

    // fetchAll() : recherche multicritères positionnelle
    $conditions = ["1=1"];
    $params     = [];

    if ($medecin !== '') { $conditions[] = "medecin_nom LIKE ?"; $params[] = "%$medecin%"; }
    if ($jour    !== '') { $conditions[] = "jour LIKE ?";        $params[] = "%$jour%"; }

    $stmt = $pdo->prepare("SELECT * FROM horaires WHERE " . implode(" AND ", $conditions) . " ORDER BY medecin_nom, jour");
    $stmt->execute($params);
    $resultats = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche Horaires - QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 950px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #5cb85c; }
        .search-form { background: #f0fff4; padding: 20px; border-radius: 8px; margin-bottom: 25px; }
        .search-form label { font-weight: bold; display: block; margin-top: 10px; }
        .search-form input { width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc; margin-top: 4px; box-sizing: border-box; }
        .search-form button { margin-top: 15px; padding: 10px 25px; background: #5cb85c; color: white; border: none; border-radius: 6px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #5cb85c; color: white; padding: 10px; }
        td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        tr:nth-child(even) { background: #f9f9f9; }
        .stats { background: #e8f5e9; padding: 10px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; }
        .objet-box { background: #fffbe6; border: 1px solid #f0c040; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
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
        <a href="../pages/details1.html">← Retour</a>
    </div>

    <h2>🔍 Recherche dans les Horaires</h2>
    <div class="stats">📊 Total des horaires : <?= $total ?></div>

    <div class="search-form">
        <form method="POST">
            <label>Recherche par ID exact (fetchObject) :</label>
            <input type="number" name="id_exact" min="1" placeholder="Ex: 1" value="<?= htmlspecialchars($_POST['id_exact'] ?? '') ?>">
            <label>Nom du médecin :</label>
            <input type="text" name="medecin" placeholder="Ex: Ali" value="<?= htmlspecialchars($_POST['medecin'] ?? '') ?>">
            <label>Jour :</label>
            <input type="text" name="jour" placeholder="Ex: Lundi" value="<?= htmlspecialchars($_POST['jour'] ?? '') ?>">
            <button type="submit">Rechercher</button>
        </form>
    </div>

    <?php if ($recherche): ?>
        <?php if (($_POST['id_exact'] ?? 0) > 0): ?>
            <h3>Résultat par ID (fetchObject) :</h3>
            <?php if ($resultatObjet): ?>
                <div class="objet-box">
                    <p><strong>ID :</strong> <?= $resultatObjet->id ?> | <strong>Médecin :</strong> <?= htmlspecialchars($resultatObjet->medecin_nom) ?></p>
                    <p><strong>Jour :</strong> <?= htmlspecialchars($resultatObjet->jour) ?> | <strong>Matin :</strong> <?= htmlspecialchars($resultatObjet->matin) ?> | <strong>Après-midi :</strong> <?= htmlspecialchars($resultatObjet->apres_midi) ?></p>
                </div>
            <?php else: ?>
                <p style="color:red;">Aucun horaire trouvé avec cet ID.</p>
            <?php endif; ?>
        <?php endif; ?>

        <h3>Résultats (<?= count($resultats) ?> trouvés) :</h3>
        <?php if (empty($resultats)): ?>
            <p style="color:gray;">Aucun résultat.</p>
        <?php else: ?>
            <table>
                <thead><tr><th>#</th><th>Médecin</th><th>Jour</th><th>Matin</th><th>Après-midi</th><th>Photo</th></tr></thead>
                <tbody>
                    <?php foreach ($resultats as $h): ?>
                        <tr>
                            <td><?= $h['id'] ?></td>
                            <td><?= htmlspecialchars($h['medecin_nom']) ?></td>
                            <td><?= htmlspecialchars($h['jour']) ?></td>
                            <td><?= htmlspecialchars($h['matin']) ?></td>
                            <td><?= htmlspecialchars($h['apres_midi']) ?></td>
                            <td><img src="../<?= htmlspecialchars($h['photo']) ?>" width="50" style="border-radius:50%;"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
