<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : modification_question.php
    Rôle    : Modifier une question existante
              Utilise : prepare() + execute() nommé pour UPDATE
                        prepare() + execute() positionnel pour SELECT
*/

require_once 'connexion.php';

$question = null;
$erreurs  = [];
$succes   = false;

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
    $stmt->execute([intval($_GET['id'])]);
    $question = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id         = intval($_POST['id']         ?? 0);
    $definition = trim($_POST['definition']   ?? '');
    $reponse    = trim($_POST['reponse']      ?? '');
    $difficulte = trim($_POST['difficulte']   ?? '');
    $photo      = trim($_POST['photo']        ?? '');

    $stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
    $stmt->execute([$id]);
    $question = $stmt->fetch();

    if (strlen($definition) < 5) $erreurs['definition'] = "Définition trop courte (min 5 caractères).";
    if (empty($reponse))         $erreurs['reponse']    = "Réponse obligatoire.";
    if (!in_array($difficulte, ['facile', 'moyen', 'difficile'])) $erreurs['difficulte'] = "Difficulté invalide.";
    if (empty($photo))           $erreurs['photo']      = "Photo obligatoire.";

    if (empty($erreurs)) {
        $stmt = $pdo->prepare("UPDATE questions SET definition=:definition, reponse=:reponse, difficulte=:difficulte, photo=:photo WHERE id=:id");
        $stmt->execute([':definition' => $definition, ':reponse' => $reponse, ':difficulte' => $difficulte, ':photo' => $photo, ':id' => $id]);
        $succes   = true;
        $question = array_merge($question, compact('definition', 'reponse', 'difficulte', 'photo'));
    }
}

$toutes = $pdo->query("SELECT id, definition, difficulte FROM questions ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modification Question - QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .page-wrapper { display: flex; gap: 20px; max-width: 1100px; margin: 30px auto; }
        .sidebar { width: 240px; background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); flex-shrink: 0; max-height: 85vh; overflow-y: auto; }
        .sidebar h4 { color: #8e44ad; margin-top: 0; }
        .sidebar a { display: block; padding: 6px 8px; color: #333; text-decoration: none; border-radius: 5px; margin-bottom: 4px; font-size: 13px; }
        .sidebar a:hover { background: #f5eefb; color: #8e44ad; }
        .container { flex: 1; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #8e44ad; }
        label { font-weight: bold; display: block; margin-top: 12px; }
        input[type="text"], textarea, select { width: 100%; padding: 9px; border-radius: 5px; border: 1px solid #ccc; margin-top: 4px; box-sizing: border-box; }
        .err { color: red; font-size: 13px; }
        .succes-msg { background: #e6f4ea; color: green; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        button[type="submit"] { margin-top: 20px; padding: 10px 25px; background: #8e44ad; color: white; border: none; border-radius: 6px; cursor: pointer; width: 100%; }
        button[type="submit"]:hover { background: #6c3483; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-bar input { flex: 1; padding: 9px; border-radius: 5px; border: 1px solid #ccc; }
        .search-bar button { padding: 9px 20px; background: #8e44ad; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .nav-links a { margin-right: 15px; color: #8e44ad; text-decoration: none; font-weight: bold; }
        .badge { padding: 2px 8px; border-radius: 10px; font-size: 12px; }
        .badge-facile { background: #e6f4ea; color: green; }
        .badge-moyen { background: #fff3cd; color: #856404; }
        .badge-difficile { background: #fdecea; color: red; }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="sidebar">
        <h4>📋 Questions</h4>
        <?php foreach ($toutes as $q): ?>
            <a href="modification_question.php?id=<?= $q['id'] ?>">
                #<?= $q['id'] ?> <?= htmlspecialchars(substr($q['definition'], 0, 35)) ?>...
                <span class="badge badge-<?= $q['difficulte'] ?>"><?= ucfirst($q['difficulte']) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="container">
        <div class="nav-links">
            <a href="insertion_question.php">➕ Ajouter</a>
            <a href="recherche_questions.php">🔍 Rechercher</a>
            <a href="modification_question.php">✏️ Modifier</a>
            <a href="suppression_question.php">🗑️ Supprimer</a>
            <a href="../pages/funpage.html">← Retour</a>
        </div>
        <h2>✏️ Modifier une question</h2>
        <form method="GET">
            <div class="search-bar">
                <input type="number" name="id" min="1" placeholder="ID de la question" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
                <button type="submit">Charger</button>
            </div>
        </form>
        <?php if ($succes): ?><div class="succes-msg">✅ Question modifiée avec succès !</div><?php endif; ?>
        <?php if ($question): ?>
            <form method="POST">
                <input type="hidden" name="id" value="<?= $question['id'] ?>">
                <label>Définition :</label>
                <textarea name="definition" rows="3"><?= htmlspecialchars($question['definition']) ?></textarea>
                <?php if (isset($erreurs['definition'])): ?><span class="err">⚠ <?= $erreurs['definition'] ?></span><?php endif; ?>
                <label>Réponse :</label>
                <input type="text" name="reponse" value="<?= htmlspecialchars($question['reponse']) ?>">
                <?php if (isset($erreurs['reponse'])): ?><span class="err">⚠ <?= $erreurs['reponse'] ?></span><?php endif; ?>
                <label>Difficulté :</label>
                <select name="difficulte">
                    <option value="facile"    <?= $question['difficulte'] === 'facile'    ? 'selected' : '' ?>>Facile</option>
                    <option value="moyen"     <?= $question['difficulte'] === 'moyen'     ? 'selected' : '' ?>>Moyen</option>
                    <option value="difficile" <?= $question['difficulte'] === 'difficile' ? 'selected' : '' ?>>Difficile</option>
                </select>
                <?php if (isset($erreurs['difficulte'])): ?><span class="err">⚠ <?= $erreurs['difficulte'] ?></span><?php endif; ?>
                <label>Photo :</label>
                <input type="text" name="photo" value="<?= htmlspecialchars($question['photo']) ?>">
                <?php if (isset($erreurs['photo'])): ?><span class="err">⚠ <?= $erreurs['photo'] ?></span><?php endif; ?>
                <button type="submit">Enregistrer</button>
            </form>
        <?php elseif (!isset($_GET['id'])): ?>
            <p style="color:gray;">← Sélectionnez une question ou entrez un ID.</p>
        <?php else: ?>
            <p style="color:red;">Aucune question trouvée avec cet ID.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
