<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : insertion_question.php
    Rôle    : Ajouter une nouvelle question dans la table questions
              Utilise : prepare() + execute() nommé
*/

require_once 'connexion.php';

$erreurs = [];
$succes  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $definition = trim($_POST['definition'] ?? '');
    $reponse    = trim($_POST['reponse']    ?? '');
    $difficulte = trim($_POST['difficulte'] ?? '');
    $photo      = trim($_POST['photo']      ?? '');

    if (strlen($definition) < 5)
        $erreurs['definition'] = "La définition doit contenir au moins 5 caractères.";
    if (empty($reponse))
        $erreurs['reponse'] = "La réponse est obligatoire.";
    if (!in_array($difficulte, ['facile', 'moyen', 'difficile']))
        $erreurs['difficulte'] = "Veuillez choisir une difficulté valide.";
    if (empty($photo))
        $erreurs['photo'] = "Le chemin de la photo est obligatoire.";

    if (empty($erreurs)) {
        $stmt = $pdo->prepare("INSERT INTO questions (definition, reponse, difficulte, photo) VALUES (:definition, :reponse, :difficulte, :photo)");
        $stmt->execute([':definition' => $definition, ':reponse' => $reponse, ':difficulte' => $difficulte, ':photo' => $photo]);
        $succes = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Insertion Question - QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 650px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #8e44ad; }
        label { font-weight: bold; display: block; margin-top: 12px; }
        input[type="text"], textarea, select { width: 100%; padding: 9px; border-radius: 5px; border: 1px solid #ccc; margin-top: 4px; box-sizing: border-box; }
        .err { color: red; font-size: 13px; }
        .succes-msg { background: #e6f4ea; color: green; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        button[type="submit"] { margin-top: 20px; padding: 10px 25px; background: #8e44ad; color: white; border: none; border-radius: 6px; cursor: pointer; width: 100%; }
        button[type="submit"]:hover { background: #6c3483; }
        .nav-links a { margin-right: 15px; color: #8e44ad; text-decoration: none; font-weight: bold; }
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
    <h2>➕ Ajouter une question</h2>
    <?php if ($succes): ?><div class="succes-msg">✅ Question ajoutée ! <a href="recherche_questions.php">Voir toutes</a></div><?php endif; ?>
    <form method="POST">
        <label>Définition :</label>
        <textarea name="definition" rows="3" placeholder="Ex: Médecin du cœur"><?= htmlspecialchars($_POST['definition'] ?? '') ?></textarea>
        <?php if (isset($erreurs['definition'])): ?><span class="err">⚠ <?= $erreurs['definition'] ?></span><?php endif; ?>

        <label>Réponse :</label>
        <input type="text" name="reponse" placeholder="Ex: cardiologue" value="<?= htmlspecialchars($_POST['reponse'] ?? '') ?>">
        <?php if (isset($erreurs['reponse'])): ?><span class="err">⚠ <?= $erreurs['reponse'] ?></span><?php endif; ?>

        <label>Difficulté :</label>
        <select name="difficulte">
            <option value="">-- Choisir --</option>
            <option value="facile"    <?= (($_POST['difficulte'] ?? '') === 'facile')    ? 'selected' : '' ?>>Facile</option>
            <option value="moyen"     <?= (($_POST['difficulte'] ?? '') === 'moyen')     ? 'selected' : '' ?>>Moyen</option>
            <option value="difficile" <?= (($_POST['difficulte'] ?? '') === 'difficile') ? 'selected' : '' ?>>Difficile</option>
        </select>
        <?php if (isset($erreurs['difficulte'])): ?><span class="err">⚠ <?= $erreurs['difficulte'] ?></span><?php endif; ?>

        <label>Photo (chemin, ex: images/doctor1.jpg) :</label>
        <input type="text" name="photo" placeholder="images/doctor1.jpg" value="<?= htmlspecialchars($_POST['photo'] ?? '') ?>">
        <?php if (isset($erreurs['photo'])): ?><span class="err">⚠ <?= $erreurs['photo'] ?></span><?php endif; ?>

        <button type="submit">Enregistrer</button>
    </form>
</div>
</body>
</html>
