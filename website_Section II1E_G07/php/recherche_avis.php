<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : recherche_avis.php
    Rôle    : Recherche dans la table avis selon plusieurs critères
              Utilise : query(), fetch(), fetchAll(), fetchObject()
*/

require_once 'connexion.php';

$resultats     = [];
$resultatObjet = null;
$recherche     = false;
$totalAvis     = 0;

// --- Récupérer le nombre total d'avis avec query() (requête simple sans paramètre) ---
$stmtTotal  = $pdo->query("SELECT COUNT(*) AS total FROM avis");
$totalAvis  = $stmtTotal->fetch()['total'];  // fetch() → une seule ligne

// --- Traitement de la recherche ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recherche  = true;
    $nom        = trim($_POST['nom']        ?? '');
    $satisfait  = trim($_POST['satisfait']  ?? '');
    $note_min   = intval($_POST['note_min'] ?? 1);
    $note_max   = intval($_POST['note_max'] ?? 5);
    $id_exact   = intval($_POST['id_exact'] ?? 0);

    // --- Recherche par ID exact avec prepare() + execute() nommé → fetchObject() ---
    if ($id_exact > 0) {
        $stmt = $pdo->prepare("SELECT * FROM avis WHERE id = :id");
        $stmt->execute([':id' => $id_exact]);
        $resultatObjet = $stmt->fetchObject(); // fetchObject() → retourne un objet stdClass
    }

    // --- Recherche multicritères avec prepare() + execute() positionnel → fetchAll() ---
    $conditions = ["note BETWEEN ? AND ?"];
    $params     = [$note_min, $note_max];

    if ($nom !== '') {
        $conditions[] = "nom LIKE ?";
        $params[]     = "%$nom%";
    }
    if ($satisfait !== '') {
        $conditions[] = "satisfait = ?";
        $params[]     = $satisfait;
    }

    $sql  = "SELECT * FROM avis WHERE " . implode(" AND ", $conditions) . " ORDER BY date_avis DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);           // execute() positionnel
    $resultats = $stmt->fetchAll();    // fetchAll() → tous les résultats
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche Avis - QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1000px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #2c6ed5; }
        .search-form { background: #f0f4ff; padding: 20px; border-radius: 8px; margin-bottom: 25px; }
        .search-form label { font-weight: bold; display: block; margin-top: 10px; }
        .search-form input, .search-form select { width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc; margin-top: 4px; box-sizing: border-box; }
        .search-form button { margin-top: 15px; padding: 10px 25px; background: #2c6ed5; color: white; border: none; border-radius: 6px; cursor: pointer; }
        .search-form button:hover { background: #1fa2ff; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #2c6ed5; color: white; padding: 10px; }
        td { padding: 10px; border: 1px solid #ddd; }
        tr:nth-child(even) { background: #f9f9f9; }
        .badge-oui { background: #e6f4ea; color: green; padding: 3px 8px; border-radius: 10px; }
        .badge-non { background: #fdecea; color: red; padding: 3px 8px; border-radius: 10px; }
        .objet-box { background: #fffbe6; border: 1px solid #f0c040; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .stats { background: #e8f0fe; padding: 10px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; }
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

    <h2>🔍 Recherche dans les Avis</h2>

    <!-- Statistique globale via query() + fetch() -->
    <div class="stats">📊 Total des avis enregistrés : <?= $totalAvis ?></div>

    <!-- Formulaire de recherche -->
    <div class="search-form">
        <form method="POST">
            <label>Rechercher par ID exact (fetchObject) :</label>
            <input type="number" name="id_exact" min="1" placeholder="Ex: 1" value="<?= htmlspecialchars($_POST['id_exact'] ?? '') ?>">

            <label>Nom du patient (recherche partielle) :</label>
            <input type="text" name="nom" placeholder="Ex: Ahmed" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">

            <label>Satisfaction :</label>
            <select name="satisfait">
                <option value="">-- Tous --</option>
                <option value="oui" <?= (($_POST['satisfait'] ?? '') === 'oui') ? 'selected' : '' ?>>Satisfait</option>
                <option value="non" <?= (($_POST['satisfait'] ?? '') === 'non') ? 'selected' : '' ?>>Non satisfait</option>
            </select>

            <label>Note minimale :</label>
            <input type="number" name="note_min" min="1" max="5" value="<?= htmlspecialchars($_POST['note_min'] ?? '1') ?>">

            <label>Note maximale :</label>
            <input type="number" name="note_max" min="1" max="5" value="<?= htmlspecialchars($_POST['note_max'] ?? '5') ?>">

            <button type="submit">Rechercher</button>
        </form>
    </div>

    <?php if ($recherche): ?>

        <!-- Résultat fetchObject() -->
        <?php if ($_POST['id_exact'] > 0): ?>
            <h3>Résultat par ID (fetchObject) :</h3>
            <?php if ($resultatObjet): ?>
                <div class="objet-box">
                    <p><strong>ID :</strong> <?= $resultatObjet->id ?></p>
                    <p><strong>Nom :</strong> <?= htmlspecialchars($resultatObjet->nom) ?></p>
                    <p><strong>Email :</strong> <?= htmlspecialchars($resultatObjet->email) ?></p>
                    <p><strong>Note :</strong> <?= str_repeat('★', $resultatObjet->note) ?></p>
                    <p><strong>Commentaire :</strong> <?= htmlspecialchars($resultatObjet->commentaire) ?></p>
                </div>
            <?php else: ?>
                <p style="color:red;">Aucun avis trouvé avec l'ID <?= intval($_POST['id_exact']) ?>.</p>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Résultats fetchAll() -->
        <h3>Résultats de la recherche (<?= count($resultats) ?> avis trouvés) :</h3>
        <?php if (empty($resultats)): ?>
            <p style="color:gray;">Aucun résultat correspondant.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>#</th><th>Nom</th><th>Email</th><th>Satisfait</th><th>Services</th><th>Note</th><th>Commentaire</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($resultats as $row): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['nom']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><span class="badge-<?= $row['satisfait'] ?>"><?= $row['satisfait'] === 'oui' ? '✅ Oui' : '❌ Non' ?></span></td>
                            <td><?= htmlspecialchars($row['services']) ?></td>
                            <td style="color:gold;"><?= str_repeat('★', $row['note']) . str_repeat('☆', 5 - $row['note']) ?></td>
                            <td><?= htmlspecialchars($row['commentaire']) ?></td>
                            <td><?= htmlspecialchars($row['date_avis']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php endif; ?>

</div>
</body>
</html>
