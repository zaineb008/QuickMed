<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : modification_avis.php
    Rôle    : Modifier un avis existant (note, commentaire, satisfaction)
              Utilise : prepare() + execute() nommé pour UPDATE
                        prepare() + execute() positionnel pour SELECT
*/

require_once 'connexion.php';

$avis    = null;
$erreurs = [];
$succes  = false;

// Étape 1 : Charger l'avis à modifier via son ID (GET)
if (isset($_GET['id'])) {
    $id   = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM avis WHERE id = ?");  // positionnel
    $stmt->execute([$id]);
    $avis = $stmt->fetch();
}

// Étape 2 : Traiter la modification (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = intval($_POST['id']          ?? 0);
    $satisfait   = trim($_POST['satisfait']     ?? '');
    $services    = $_POST['services']           ?? [];
    $note        = intval($_POST['note']        ?? 0);
    $commentaire = trim($_POST['commentaire']   ?? '');

    // Recharger l'avis pour pré-remplir le formulaire en cas d'erreur
    $stmt = $pdo->prepare("SELECT * FROM avis WHERE id = ?");
    $stmt->execute([$id]);
    $avis = $stmt->fetch();

    // Validation PHP
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
        // prepare() + execute() nommé pour UPDATE
        $stmt = $pdo->prepare("
            UPDATE avis
            SET satisfait   = :satisfait,
                services    = :services,
                note        = :note,
                commentaire = :commentaire
            WHERE id = :id
        ");
        $stmt->execute([
            ':satisfait'   => $satisfait,
            ':services'    => implode(',', $services),
            ':note'        => $note,
            ':commentaire' => $commentaire,
            ':id'          => $id,
        ]);

        $succes = true;
        // Recharger les données mises à jour
        $stmt = $pdo->prepare("SELECT * FROM avis WHERE id = ?");
        $stmt->execute([$id]);
        $avis = $stmt->fetch();
    }
}

// Liste de tous les avis pour navigation rapide
$tousLesAvis = $pdo->query("SELECT id, nom, note FROM avis ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modification Avis - QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .page-wrapper { display: flex; gap: 20px; max-width: 1100px; margin: 30px auto; }
        .sidebar { width: 220px; background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); flex-shrink: 0; }
        .sidebar h4 { color: #2c6ed5; margin-top: 0; }
        .sidebar a { display: block; padding: 6px 8px; color: #333; text-decoration: none; border-radius: 5px; margin-bottom: 4px; }
        .sidebar a:hover { background: #e8f0fe; color: #2c6ed5; }
        .container { flex: 1; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #f0ad4e; }
        label { font-weight: bold; display: block; margin-top: 12px; }
        input[type="text"], input[type="email"], textarea, select {
            width: 100%; padding: 9px; border-radius: 5px; border: 1px solid #ccc; margin-top: 4px; box-sizing: border-box;
        }
        .err { color: red; font-size: 13px; }
        .succes-msg { background: #e6f4ea; color: green; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        button[type="submit"] { margin-top: 20px; padding: 10px 25px; background: #f0ad4e; color: white; border: none; border-radius: 6px; cursor: pointer; width: 100%; font-size: 15px; }
        button[type="submit"]:hover { background: #ec971f; }
        .info-readonly { background: #f8f9fa; padding: 10px; border-radius: 6px; margin-bottom: 10px; border: 1px solid #dee2e6; }
        .rating-group label { display: inline; font-weight: normal; margin-right: 10px; }
        .nav-links a { margin-right: 15px; color: #2c6ed5; text-decoration: none; font-weight: bold; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-bar input { flex: 1; padding: 9px; border-radius: 5px; border: 1px solid #ccc; }
        .search-bar button { padding: 9px 20px; background: #2c6ed5; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
<div class="page-wrapper">

    <!-- Sidebar : liste des avis -->
    <div class="sidebar">
        <h4>📋 Tous les avis</h4>
        <?php foreach ($tousLesAvis as $a): ?>
            <a href="modification_avis.php?id=<?= $a['id'] ?>">
                #<?= $a['id'] ?> – <?= htmlspecialchars($a['nom']) ?> (<?= str_repeat('★', $a['note']) ?>)
            </a>
        <?php endforeach; ?>
    </div>

    <div class="container">

        <div class="nav-links">
            <a href="insertion_avis.php">➕ Ajouter</a>
            <a href="recherche_avis.php">🔍 Rechercher</a>
            <a href="modification_avis.php">✏️ Modifier</a>
            <a href="suppression_avis.php">🗑️ Supprimer</a>
            <a href="../pages/questionnaire.html">← Retour</a>
        </div>

        <h2>✏️ Modifier un avis</h2>

        <!-- Recherche par ID -->
        <form method="GET">
            <div class="search-bar">
                <input type="number" name="id" min="1" placeholder="Entrez l'ID de l'avis à modifier" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
                <button type="submit">Charger</button>
            </div>
        </form>

        <?php if ($succes): ?>
            <div class="succes-msg">✅ Avis #<?= intval($_POST['id']) ?> modifié avec succès !</div>
        <?php endif; ?>

        <?php if ($avis): ?>

            <!-- Champs non modifiables (informatifs) -->
            <div class="info-readonly">
                <strong>Nom :</strong> <?= htmlspecialchars($avis['nom']) ?> &nbsp;|&nbsp;
                <strong>Email :</strong> <?= htmlspecialchars($avis['email']) ?> &nbsp;|&nbsp;
                <strong>Date :</strong> <?= htmlspecialchars($avis['date_avis']) ?>
            </div>

            <form method="POST">
                <input type="hidden" name="id" value="<?= $avis['id'] ?>">

                <label>Satisfaction :</label>
                <select name="satisfait">
                    <option value="oui" <?= $avis['satisfait'] === 'oui' ? 'selected' : '' ?>>Oui</option>
                    <option value="non" <?= $avis['satisfait'] === 'non' ? 'selected' : '' ?>>Non</option>
                </select>
                <?php if (isset($erreurs['satisfait'])): ?><span class="err">⚠ <?= $erreurs['satisfait'] ?></span><?php endif; ?>

                <label>Services :</label>
                <?php
                $servicesActuels = explode(',', $avis['services']);
                foreach (['consultation' => 'Consultation', 'rdv' => 'Rendez-vous', 'teleconsultation' => 'Téléconsultation'] as $val => $label):
                    $checked = in_array($val, $servicesActuels) ? 'checked' : '';
                ?>
                    <label class="rating-group"><input type="checkbox" name="services[]" value="<?= $val ?>" <?= $checked ?>> <?= $label ?></label>
                <?php endforeach; ?>
                <?php if (isset($erreurs['services'])): ?><span class="err">⚠ <?= $erreurs['services'] ?></span><?php endif; ?>

                <label>Note (1 à 5) :</label>
                <div class="rating-group">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <label><input type="radio" name="note" value="<?= $i ?>" <?= $avis['note'] == $i ? 'checked' : '' ?>> <?= $i ?>★</label>
                    <?php endfor; ?>
                </div>
                <?php if (isset($erreurs['note'])): ?><span class="err">⚠ <?= $erreurs['note'] ?></span><?php endif; ?>

                <label>Commentaire :</label>
                <textarea name="commentaire" rows="4"><?= htmlspecialchars($avis['commentaire']) ?></textarea>
                <?php if (isset($erreurs['commentaire'])): ?><span class="err">⚠ <?= $erreurs['commentaire'] ?></span><?php endif; ?>

                <button type="submit">Enregistrer les modifications</button>
            </form>

        <?php elseif (!isset($_GET['id'])): ?>
            <p style="color:gray;">← Sélectionnez un avis dans la liste ou entrez un ID.</p>
        <?php else: ?>
            <p style="color:red;">Aucun avis trouvé avec cet ID.</p>
        <?php endif; ?>

    </div>
</div>
</body>
</html>
