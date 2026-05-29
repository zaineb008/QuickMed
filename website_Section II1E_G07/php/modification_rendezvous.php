<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : modification_rendezvous.php
    Rôle    : Modifier un rendez-vous existant (date, heure, type, téléphone)
              Utilise : prepare() + execute() nommé pour UPDATE
                        prepare() + execute() positionnel pour SELECT
*/

require_once 'connexion.php';

$rdv     = null;
$erreurs = [];
$succes  = false;

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM rendezvous WHERE id = ?");
    $stmt->execute([intval($_GET['id'])]);
    $rdv = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id                = intval($_POST['id']                ?? 0);
    $telephone         = trim($_POST['telephone']           ?? '');
    $date_rdv          = trim($_POST['date_rdv']            ?? '');
    $heure_rdv         = trim($_POST['heure_rdv']           ?? '');
    $type_consultation = trim($_POST['type_consultation']   ?? '');

    $stmt = $pdo->prepare("SELECT * FROM rendezvous WHERE id = ?");
    $stmt->execute([$id]);
    $rdv = $stmt->fetch();

    if (!preg_match('/^\+?[0-9]{8,15}$/', $telephone)) $erreurs['telephone'] = "Téléphone invalide.";
    if (empty($date_rdv) || $date_rdv < date('Y-m-d')) $erreurs['date_rdv']  = "Date invalide (aujourd'hui ou futur).";
    if (empty($heure_rdv)) $erreurs['heure_rdv'] = "Heure obligatoire.";
    if (!in_array($type_consultation, ['consultation', 'urgence'])) $erreurs['type_consultation'] = "Type invalide.";

    if (empty($erreurs)) {
        $stmt = $pdo->prepare("UPDATE rendezvous SET telephone=:telephone, date_rdv=:date_rdv, heure_rdv=:heure_rdv, type_consultation=:type_consultation WHERE id=:id");
        $stmt->execute([':telephone' => $telephone, ':date_rdv' => $date_rdv, ':heure_rdv' => $heure_rdv, ':type_consultation' => $type_consultation, ':id' => $id]);
        $succes = true;
        $rdv    = array_merge($rdv, compact('telephone', 'date_rdv', 'heure_rdv', 'type_consultation'));
    }
}

$tous = $pdo->query("SELECT id, nom, prenom, date_rdv, type_consultation FROM rendezvous ORDER BY date_rdv ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modification Rendez-vous - QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .page-wrapper { display: flex; gap: 20px; max-width: 1100px; margin: 30px auto; }
        .sidebar { width: 240px; background: white; padding: 15px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); flex-shrink: 0; max-height: 85vh; overflow-y: auto; }
        .sidebar h4 { color: #5cb85c; margin-top: 0; }
        .sidebar a { display: block; padding: 6px 8px; color: #333; text-decoration: none; border-radius: 5px; margin-bottom: 4px; font-size: 13px; }
        .sidebar a:hover { background: #f0faf0; color: #5cb85c; }
        .container { flex: 1; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #5cb85c; }
        label { font-weight: bold; display: block; margin-top: 12px; }
        input, select { width: 100%; padding: 9px; border-radius: 5px; border: 1px solid #ccc; margin-top: 4px; box-sizing: border-box; }
        .err { color: red; font-size: 13px; }
        .succes-msg { background: #e6f4ea; color: green; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        button[type="submit"] { margin-top: 20px; padding: 10px 25px; background: #5cb85c; color: white; border: none; border-radius: 6px; cursor: pointer; width: 100%; }
        button[type="submit"]:hover { background: #449d44; }
        .info-readonly { background: #f8f9fa; padding: 12px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #dee2e6; font-size: 14px; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-bar input { flex: 1; padding: 9px; border-radius: 5px; border: 1px solid #ccc; }
        .search-bar button { padding: 9px 20px; background: #5cb85c; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .nav-links a { margin-right: 15px; color: #5cb85c; text-decoration: none; font-weight: bold; }
        .badge { padding: 2px 8px; border-radius: 10px; font-size: 12px; }
        .badge-consultation { background: #d4edda; color: #155724; }
        .badge-urgence      { background: #f8d7da; color: #721c24; }
        .form-row { display: flex; gap: 15px; }
        .form-row > div { flex: 1; }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="sidebar">
        <h4>📋 Rendez-vous</h4>
        <?php foreach ($tous as $r): ?>
            <a href="modification_rendezvous.php?id=<?= $r['id'] ?>">
                #<?= $r['id'] ?> <?= htmlspecialchars($r['prenom']) ?> <?= htmlspecialchars($r['nom']) ?><br>
                <small><?= htmlspecialchars($r['date_rdv']) ?> <span class="badge badge-<?= $r['type_consultation'] ?>"><?= ucfirst($r['type_consultation']) ?></span></small>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="container">
        <div class="nav-links">
            <a href="insertion_rendezvous.php">➕ Ajouter</a>
            <a href="recherche_rendezvous.php">🔍 Rechercher</a>
            <a href="modification_rendezvous.php">✏️ Modifier</a>
            <a href="suppression_rendezvous.php">🗑️ Supprimer</a>
            <a href="../pages/rendezvous.html">← Retour</a>
        </div>
        <h2>✏️ Modifier un rendez-vous</h2>
        <form method="GET">
            <div class="search-bar">
                <input type="number" name="id" min="1" placeholder="ID du rendez-vous" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
                <button type="submit">Charger</button>
            </div>
        </form>
        <?php if ($succes): ?><div class="succes-msg">✅ Rendez-vous modifié avec succès !</div><?php endif; ?>
        <?php if ($rdv): ?>
            <div class="info-readonly">
                <strong>Patient :</strong> <?= htmlspecialchars($rdv['nom']) ?> <?= htmlspecialchars($rdv['prenom']) ?>
                &nbsp;|&nbsp; <strong>Email :</strong> <?= htmlspecialchars($rdv['email']) ?>
                &nbsp;|&nbsp; <strong>Sexe :</strong> <?= ucfirst($rdv['sexe']) ?>
            </div>
            <form method="POST">
                <input type="hidden" name="id" value="<?= $rdv['id'] ?>">
                <label>Téléphone :</label>
                <input type="text" name="telephone" value="<?= htmlspecialchars($rdv['telephone']) ?>">
                <?php if (isset($erreurs['telephone'])): ?><span class="err">⚠ <?= $erreurs['telephone'] ?></span><?php endif; ?>
                <div class="form-row">
                    <div>
                        <label>Date :</label>
                        <input type="date" name="date_rdv" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($rdv['date_rdv']) ?>">
                        <?php if (isset($erreurs['date_rdv'])): ?><span class="err">⚠ <?= $erreurs['date_rdv'] ?></span><?php endif; ?>
                    </div>
                    <div>
                        <label>Heure :</label>
                        <input type="time" name="heure_rdv" value="<?= htmlspecialchars($rdv['heure_rdv']) ?>">
                        <?php if (isset($erreurs['heure_rdv'])): ?><span class="err">⚠ <?= $erreurs['heure_rdv'] ?></span><?php endif; ?>
                    </div>
                </div>
                <label>Type de consultation :</label>
                <select name="type_consultation">
                    <option value="consultation" <?= $rdv['type_consultation'] === 'consultation' ? 'selected' : '' ?>>Consultation</option>
                    <option value="urgence"      <?= $rdv['type_consultation'] === 'urgence'      ? 'selected' : '' ?>>Urgence</option>
                </select>
                <?php if (isset($erreurs['type_consultation'])): ?><span class="err">⚠ <?= $erreurs['type_consultation'] ?></span><?php endif; ?>
                <button type="submit">Enregistrer</button>
            </form>
        <?php elseif (!isset($_GET['id'])): ?>
            <p style="color:gray;">← Sélectionnez un rendez-vous ou entrez un ID.</p>
        <?php else: ?>
            <p style="color:red;">Aucun rendez-vous trouvé avec cet ID.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
