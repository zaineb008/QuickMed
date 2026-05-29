<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : modification_horaire.php
    Rôle    : Modifier un horaire existant
              Utilise : prepare() + execute() nommé pour UPDATE
*/

require_once 'connexion.php';

$horaire = null;
$erreurs = [];
$succes  = false;

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM horaires WHERE id = ?");
    $stmt->execute([intval($_GET['id'])]);
    $horaire = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id         = intval($_POST['id']         ?? 0);
    $jour       = trim($_POST['jour']         ?? '');
    $matin      = trim($_POST['matin']        ?? '');
    $apres_midi = trim($_POST['apres_midi']   ?? '');
    $medecin    = trim($_POST['medecin_nom']  ?? '');

    $stmt = $pdo->prepare("SELECT * FROM horaires WHERE id = ?");
    $stmt->execute([$id]);
    $horaire = $stmt->fetch();

    if (empty($jour))    $erreurs['jour']    = "Jour obligatoire.";
    if (empty($matin))   $erreurs['matin']   = "Matin obligatoire.";
    if (empty($apres_midi)) $erreurs['apres_midi'] = "Après-midi obligatoire.";
    if (empty($medecin)) $erreurs['medecin'] = "Médecin obligatoire.";

    if (empty($erreurs)) {
        $stmt = $pdo->prepare("UPDATE horaires SET jour=:jour, matin=:matin, apres_midi=:apres_midi, medecin_nom=:medecin_nom WHERE id=:id");
        $stmt->execute([':jour' => $jour, ':matin' => $matin, ':apres_midi' => $apres_midi, ':medecin_nom' => $medecin, ':id' => $id]);
        $succes  = true;
        $horaire = ['id' => $id, 'jour' => $jour, 'matin' => $matin, 'apres_midi' => $apres_midi, 'medecin_nom' => $medecin];
    }
}

$tous = $pdo->query("SELECT id, medecin_nom, jour FROM horaires ORDER BY medecin_nom")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><title>Modification Horaire - QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .page-wrapper { display: flex; gap: 20px; max-width: 1000px; margin: 30px auto; }
        .sidebar { width: 220px; background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); flex-shrink: 0; }
        .sidebar h4 { color: #5cb85c; margin-top: 0; }
        .sidebar a { display: block; padding: 6px 8px; color: #333; text-decoration: none; border-radius: 5px; margin-bottom: 4px; font-size: 13px; }
        .sidebar a:hover { background: #e8f5e9; }
        .container { flex: 1; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #f0ad4e; }
        label { font-weight: bold; display: block; margin-top: 12px; }
        input[type="text"] { width: 100%; padding: 9px; border-radius: 5px; border: 1px solid #ccc; margin-top: 4px; box-sizing: border-box; }
        .err { color: red; font-size: 13px; }
        .succes-msg { background: #e6f4ea; color: green; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        button[type="submit"] { margin-top: 20px; padding: 10px 25px; background: #f0ad4e; color: white; border: none; border-radius: 6px; cursor: pointer; width: 100%; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-bar input { flex: 1; padding: 9px; border-radius: 5px; border: 1px solid #ccc; }
        .search-bar button { padding: 9px 20px; background: #2c6ed5; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .nav-links a { margin-right: 15px; color: #5cb85c; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="sidebar">
        <h4>📋 Horaires</h4>
        <?php foreach ($tous as $h): ?>
            <a href="modification_horaire.php?id=<?= $h['id'] ?>">#<?= $h['id'] ?> <?= htmlspecialchars($h['medecin_nom']) ?> – <?= htmlspecialchars($h['jour']) ?></a>
        <?php endforeach; ?>
    </div>
    <div class="container">
        <div class="nav-links">
            <a href="insertion_horaire.php">➕ Ajouter</a>
            <a href="recherche_horaires.php">🔍 Rechercher</a>
            <a href="modification_horaire.php">✏️ Modifier</a>
            <a href="suppression_horaire.php">🗑️ Supprimer</a>
        </div>
        <h2>✏️ Modifier un horaire</h2>
        <form method="GET">
            <div class="search-bar">
                <input type="number" name="id" min="1" placeholder="ID de l'horaire" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
                <button type="submit">Charger</button>
            </div>
        </form>
        <?php if ($succes): ?><div class="succes-msg">✅ Horaire modifié avec succès !</div><?php endif; ?>
        <?php if ($horaire): ?>
            <form method="POST">
                <input type="hidden" name="id" value="<?= $horaire['id'] ?>">
                <label>Médecin :</label>
                <input type="text" name="medecin_nom" value="<?= htmlspecialchars($horaire['medecin_nom']) ?>">
                <?php if (isset($erreurs['medecin'])): ?><span class="err">⚠ <?= $erreurs['medecin'] ?></span><?php endif; ?>
                <label>Jour :</label>
                <input type="text" name="jour" value="<?= htmlspecialchars($horaire['jour']) ?>">
                <?php if (isset($erreurs['jour'])): ?><span class="err">⚠ <?= $erreurs['jour'] ?></span><?php endif; ?>
                <label>Matin :</label>
                <input type="text" name="matin" value="<?= htmlspecialchars($horaire['matin']) ?>">
                <?php if (isset($erreurs['matin'])): ?><span class="err">⚠ <?= $erreurs['matin'] ?></span><?php endif; ?>
                <label>Après-midi :</label>
                <input type="text" name="apres_midi" value="<?= htmlspecialchars($horaire['apres_midi']) ?>">
                <?php if (isset($erreurs['apres_midi'])): ?><span class="err">⚠ <?= $erreurs['apres_midi'] ?></span><?php endif; ?>
                <button type="submit">Enregistrer</button>
            </form>
        <?php else: ?>
            <p style="color:gray;">← Sélectionnez un horaire ou entrez un ID.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
