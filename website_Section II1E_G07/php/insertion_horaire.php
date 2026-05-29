<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : insertion_horaire.php
    Rôle    : Ajouter un nouvel horaire dans la table horaires
              Utilise : prepare() + execute() nommé
*/

require_once 'connexion.php';

$erreurs = [];
$succes  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jour       = trim($_POST['jour']        ?? '');
    $matin      = trim($_POST['matin']       ?? '');
    $apres_midi = trim($_POST['apres_midi']  ?? '');
    $medecin    = trim($_POST['medecin_nom'] ?? '');
    $photo      = trim($_POST['photo']       ?? 'images/doctor1.jpg');

    if (empty($jour))       $erreurs['jour']       = "Le jour est obligatoire.";
    if (empty($matin))      $erreurs['matin']      = "L'horaire matin est obligatoire.";
    if (empty($apres_midi)) $erreurs['apres_midi'] = "L'horaire après-midi est obligatoire.";
    if (empty($medecin))    $erreurs['medecin']    = "Le nom du médecin est obligatoire.";

    if (empty($erreurs)) {
        $stmt = $pdo->prepare("INSERT INTO horaires (jour, matin, apres_midi, medecin_nom, photo) VALUES (:jour, :matin, :apres_midi, :medecin_nom, :photo)");
        $stmt->execute([':jour' => $jour, ':matin' => $matin, ':apres_midi' => $apres_midi, ':medecin_nom' => $medecin, ':photo' => $photo]);
        $succes = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><title>Insertion Horaire - QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 600px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #5cb85c; }
        label { font-weight: bold; display: block; margin-top: 12px; }
        input[type="text"] { width: 100%; padding: 9px; border-radius: 5px; border: 1px solid #ccc; margin-top: 4px; box-sizing: border-box; }
        .err { color: red; font-size: 13px; }
        .succes-msg { background: #e6f4ea; color: green; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        button[type="submit"] { margin-top: 20px; padding: 10px 25px; background: #5cb85c; color: white; border: none; border-radius: 6px; cursor: pointer; width: 100%; }
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
    <h2>➕ Ajouter un horaire</h2>
    <?php if ($succes): ?><div class="succes-msg">✅ Horaire ajouté ! <a href="recherche_horaires.php">Voir tous</a></div><?php endif; ?>
    <form method="POST">
        <label>Nom du médecin :</label>
        <input type="text" name="medecin_nom" value="<?= htmlspecialchars($_POST['medecin_nom'] ?? '') ?>" placeholder="Ex: Dr Ali Ben Salem">
        <?php if (isset($erreurs['medecin'])): ?><span class="err">⚠ <?= $erreurs['medecin'] ?></span><?php endif; ?>

        <label>Jour :</label>
        <input type="text" name="jour" value="<?= htmlspecialchars($_POST['jour'] ?? '') ?>" placeholder="Ex: Lundi">
        <?php if (isset($erreurs['jour'])): ?><span class="err">⚠ <?= $erreurs['jour'] ?></span><?php endif; ?>

        <label>Horaire matin :</label>
        <input type="text" name="matin" value="<?= htmlspecialchars($_POST['matin'] ?? '') ?>" placeholder="Ex: 8h - 12h">
        <?php if (isset($erreurs['matin'])): ?><span class="err">⚠ <?= $erreurs['matin'] ?></span><?php endif; ?>

        <label>Horaire après-midi :</label>
        <input type="text" name="apres_midi" value="<?= htmlspecialchars($_POST['apres_midi'] ?? '') ?>" placeholder="Ex: 14h - 18h ou Fermé">
        <?php if (isset($erreurs['apres_midi'])): ?><span class="err">⚠ <?= $erreurs['apres_midi'] ?></span><?php endif; ?>

        <label>Photo (chemin) :</label>
        <input type="text" name="photo" value="<?= htmlspecialchars($_POST['photo'] ?? 'images/doctor1.jpg') ?>">

        <button type="submit">Enregistrer</button>
    </form>
</div>
</body>
</html>
