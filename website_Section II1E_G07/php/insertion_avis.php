<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : insertion_avis.php
    Rôle    : Ajouter un nouvel avis dans la table avis
              Utilise : prepare() + execute() nommé
*/

require_once 'connexion.php';

$erreurs = [];
$succes  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom         = trim($_POST['nom']         ?? '');
    $email       = trim($_POST['email']       ?? '');
    $satisfait   = trim($_POST['satisfait']   ?? '');
    $services    = $_POST['services']         ?? [];
    $note        = intval($_POST['note']      ?? 0);
    $commentaire = trim($_POST['commentaire'] ?? '');

    // Validation PHP
    if (strlen($nom) < 3 || !preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $nom))
        $erreurs['nom'] = "Nom invalide (min 3 lettres).";

    $regexEmail = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    if (!preg_match($regexEmail, $email))
        $erreurs['email'] = "Email invalide.";

    if (!in_array($satisfait, ['oui', 'non']))
        $erreurs['satisfait'] = "Satisfaction obligatoire.";

    $services = array_filter($services, function($s) {
        return in_array($s, ['consultation', 'rdv', 'teleconsultation']);
    });
    if (empty($services))
        $erreurs['services'] = "Sélectionnez au moins un service.";

    if ($note < 1 || $note > 5)
        $erreurs['note'] = "Note entre 1 et 5 obligatoire.";

    if (strlen($commentaire) < 10)
        $erreurs['commentaire'] = "Commentaire trop court (min 10 caractères).";

    if (empty($erreurs)) {
        // prepare() + execute() nommé
        $stmt = $pdo->prepare("
            INSERT INTO avis (nom, email, satisfait, services, note, commentaire)
            VALUES (:nom, :email, :satisfait, :services, :note, :commentaire)
        ");
        $stmt->execute([
            ':nom'         => $nom,
            ':email'       => $email,
            ':satisfait'   => $satisfait,
            ':services'    => implode(',', $services),
            ':note'        => $note,
            ':commentaire' => $commentaire,
        ]);
        $succes = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Insertion Avis - QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 650px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #2c6ed5; }
        label { font-weight: bold; display: block; margin-top: 12px; }
        input[type="text"], input[type="email"], textarea, select {
            width: 100%; padding: 9px; border-radius: 5px; border: 1px solid #ccc; margin-top: 4px; box-sizing: border-box;
        }
        .err { color: red; font-size: 13px; }
        button[type="submit"] { margin-top: 20px; padding: 10px 25px; background: #2c6ed5; color: white; border: none; border-radius: 6px; cursor: pointer; width: 100%; }
        button[type="submit"]:hover { background: #1fa2ff; }
        .succes-msg { background: #e6f4ea; color: green; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        .nav-links a { margin-right: 15px; color: #2c6ed5; text-decoration: none; font-weight: bold; }
        .rating-group label { display: inline; font-weight: normal; margin-right: 10px; }
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

    <h2>➕ Ajouter un nouvel avis</h2>

    <?php if ($succes): ?>
        <div class="succes-msg">✅ Avis ajouté avec succès ! <a href="recherche_avis.php">Voir tous les avis</a></div>
    <?php endif; ?>

    <form method="POST">

        <label>Nom :</label>
        <input type="text" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
        <?php if (isset($erreurs['nom'])): ?><span class="err">⚠ <?= $erreurs['nom'] ?></span><?php endif; ?>

        <label>Email :</label>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        <?php if (isset($erreurs['email'])): ?><span class="err">⚠ <?= $erreurs['email'] ?></span><?php endif; ?>

        <label>Satisfaction :</label>
        <select name="satisfait">
            <option value="">-- Choisir --</option>
            <option value="oui" <?= (($_POST['satisfait'] ?? '') === 'oui') ? 'selected' : '' ?>>Oui</option>
            <option value="non" <?= (($_POST['satisfait'] ?? '') === 'non') ? 'selected' : '' ?>>Non</option>
        </select>
        <?php if (isset($erreurs['satisfait'])): ?><span class="err">⚠ <?= $erreurs['satisfait'] ?></span><?php endif; ?>

        <label>Services utilisés :</label>
        <div class="rating-group">
            <label><input type="checkbox" name="services[]" value="consultation" <?= in_array('consultation', $_POST['services'] ?? []) ? 'checked' : '' ?>> Consultation</label>
            <label><input type="checkbox" name="services[]" value="rdv" <?= in_array('rdv', $_POST['services'] ?? []) ? 'checked' : '' ?>> Rendez-vous</label>
            <label><input type="checkbox" name="services[]" value="teleconsultation" <?= in_array('teleconsultation', $_POST['services'] ?? []) ? 'checked' : '' ?>> Téléconsultation</label>
        </div>
        <?php if (isset($erreurs['services'])): ?><span class="err">⚠ <?= $erreurs['services'] ?></span><?php endif; ?>

        <label>Note (1 à 5) :</label>
        <div class="rating-group">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <label><input type="radio" name="note" value="<?= $i ?>" <?= (($_POST['note'] ?? '') == $i) ? 'checked' : '' ?>> <?= $i ?>★</label>
            <?php endfor; ?>
        </div>
        <?php if (isset($erreurs['note'])): ?><span class="err">⚠ <?= $erreurs['note'] ?></span><?php endif; ?>

        <label>Commentaire :</label>
        <textarea name="commentaire" rows="4"><?= htmlspecialchars($_POST['commentaire'] ?? '') ?></textarea>
        <?php if (isset($erreurs['commentaire'])): ?><span class="err">⚠ <?= $erreurs['commentaire'] ?></span><?php endif; ?>

        <button type="submit">Enregistrer l'avis</button>
    </form>
</div>
</body>
</html>
