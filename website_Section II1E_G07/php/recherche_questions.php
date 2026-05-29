<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : recherche_questions.php
    Rôle    : Recherche dans la table questions selon plusieurs critères
              Utilise : query(), fetch(), fetchAll(), fetchObject()
*/

require_once 'connexion.php';

$resultats      = [];
$resultatObjet  = null;
$recherche      = false;
$totalQuestions = 0;

// Nombre total via query() + fetch()
$totalQuestions = $pdo->query("SELECT COUNT(*) AS total FROM questions")->fetch()['total'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recherche  = true;
    $definition = trim($_POST['definition'] ?? '');
    $difficulte = trim($_POST['difficulte'] ?? '');
    $id_exact   = intval($_POST['id_exact'] ?? 0);

    // fetchObject() : recherche par ID exact
    if ($id_exact > 0) {
        $stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
        $stmt->execute([$id_exact]);
        $resultatObjet = $stmt->fetchObject();
    }

    // fetchAll() : recherche multicritères positionnelle
    $conditions = ["1=1"];
    $params     = [];
    if ($definition !== '') { $conditions[] = "definition LIKE ?"; $params[] = "%$definition%"; }
    if ($difficulte !== '') { $conditions[] = "difficulte = ?";    $params[] = $difficulte; }

    $stmt = $pdo->prepare("SELECT * FROM questions WHERE " . implode(" AND ", $conditions) . " ORDER BY id DESC");
    $stmt->execute($params);
    $resultats = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche Questions - QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1000px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #8e44ad; }
        .search-form { background: #f5eefb; padding: 20px; border-radius: 8px; margin-bottom: 25px; }
        .search-form label { font-weight: bold; display: block; margin-top: 10px; }
        .search-form input, .search-form select { width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc; margin-top: 4px; box-sizing: border-box; }
        .search-form button { margin-top: 15px; padding: 10px 25px; background: #8e44ad; color: white; border: none; border-radius: 6px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #8e44ad; color: white; padding: 10px; }
        td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        tr:nth-child(even) { background: #f9f9f9; }
        .badge { padding: 3px 10px; border-radius: 10px; font-size: 13px; }
        .badge-facile    { background: #e6f4ea; color: green; }
        .badge-moyen     { background: #fff3cd; color: #856404; }
        .badge-difficile { background: #fdecea; color: red; }
        .objet-box { background: #f5eefb; border: 1px solid #8e44ad; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .stats { background: #f5eefb; padding: 10px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; color: #8e44ad; }
        .nav-links a { margin-right: 15px; color: #8e44ad; text-decoration: none; font-weight: bold; }
        img.thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; }
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

    <h2>🔍 Recherche dans les Questions</h2>
    <div class="stats">📊 Total des questions : <?= $totalQuestions ?></div>

    <div class="search-form">
        <form method="POST">
            <label>Recherche par ID exact (fetchObject) :</label>
            <input type="number" name="id_exact" min="1" placeholder="Ex: 1" value="<?= htmlspecialchars($_POST['id_exact'] ?? '') ?>">
            <label>Définition (recherche partielle) :</label>
            <input type="text" name="definition" placeholder="Ex: médecin" value="<?= htmlspecialchars($_POST['definition'] ?? '') ?>">
            <label>Difficulté :</label>
            <select name="difficulte">
                <option value="">-- Toutes --</option>
                <option value="facile"    <?= (($_POST['difficulte'] ?? '') === 'facile')    ? 'selected' : '' ?>>Facile</option>
                <option value="moyen"     <?= (($_POST['difficulte'] ?? '') === 'moyen')     ? 'selected' : '' ?>>Moyen</option>
                <option value="difficile" <?= (($_POST['difficulte'] ?? '') === 'difficile') ? 'selected' : '' ?>>Difficile</option>
            </select>
            <button type="submit">Rechercher</button>
        </form>
    </div>

    <?php if ($recherche): ?>
        <?php if (($_POST['id_exact'] ?? 0) > 0): ?>
            <h3>Résultat par ID (fetchObject) :</h3>
            <?php if ($resultatObjet): ?>
                <div class="objet-box">
                    <p><strong>ID :</strong> <?= $resultatObjet->id ?></p>
                    <p><strong>Définition :</strong> <?= htmlspecialchars($resultatObjet->definition) ?></p>
                    <p><strong>Réponse :</strong> <?= htmlspecialchars($resultatObjet->reponse) ?></p>
                    <p><strong>Difficulté :</strong> <span class="badge badge-<?= $resultatObjet->difficulte ?>"><?= ucfirst($resultatObjet->difficulte) ?></span></p>
                </div>
            <?php else: ?><p style="color:red;">Aucune question trouvée avec cet ID.</p><?php endif; ?>
        <?php endif; ?>

        <h3>Résultats (<?= count($resultats) ?> trouvée(s)) :</h3>
        <?php if (empty($resultats)): ?>
            <p style="color:gray;">Aucun résultat.</p>
        <?php else: ?>
            <table>
                <thead><tr><th>#</th><th>Définition</th><th>Réponse</th><th>Difficulté</th><th>Photo</th></tr></thead>
                <tbody>
                    <?php foreach ($resultats as $row): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td style="text-align:left;"><?= htmlspecialchars($row['definition']) ?></td>
                            <td><?= htmlspecialchars($row['reponse']) ?></td>
                            <td><span class="badge badge-<?= $row['difficulte'] ?>"><?= ucfirst($row['difficulte']) ?></span></td>
                            <td><img class="thumb" src="../<?= htmlspecialchars($row['photo']) ?>" alt="photo" onerror="this.style.display='none'"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
