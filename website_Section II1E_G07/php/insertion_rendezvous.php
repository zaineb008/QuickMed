<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : insertion_rendezvous.php
    Rôle    : Ajouter un nouveau rendez-vous dans la table rendezvous
              Utilise : prepare() + execute() nommé
*/

require_once 'connexion.php';

$erreurs = [];
$succes  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom               = trim($_POST['nom']               ?? '');
    $prenom            = trim($_POST['prenom']            ?? '');
    $email             = trim($_POST['email']             ?? '');
    $telephone         = trim($_POST['telephone']         ?? '');
    $date_rdv          = trim($_POST['date_rdv']          ?? '');
    $heure_rdv         = trim($_POST['heure_rdv']         ?? '');
    $type_consultation = trim($_POST['type_consultation'] ?? '');
    $sexe              = trim($_POST['sexe']              ?? '');

    if (strlen($nom) < 2)    $erreurs['nom']    = "Nom trop court (min 2 lettres).";
    if (strlen($prenom) < 2) $erreurs['prenom'] = "Prénom trop court (min 2 lettres).";
    $regexEmail = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    if (!preg_match($regexEmail, $email)) $erreurs['email'] = "Email invalide.";
    if (!preg_match('/^\+?[0-9]{8,15}$/', $telephone)) $erreurs['telephone'] = "Téléphone invalide (8 à 15 chiffres).";
    if (empty($date_rdv) || $date_rdv < date('Y-m-d')) $erreurs['date_rdv'] = "Date invalide (aujourd'hui ou futur).";
    if (empty($heure_rdv)) $erreurs['heure_rdv'] = "Heure obligatoire.";
    if (!in_array($type_consultation, ['consultation', 'urgence'])) $erreurs['type_consultation'] = "Type invalide.";
    if (!in_array($sexe, ['homme', 'femme'])) $erreurs['sexe'] = "Sexe invalide.";

    if (empty($erreurs)) {
        $stmt = $pdo->prepare("INSERT INTO rendezvous (nom, prenom, email, telephone, date_rdv, heure_rdv, type_consultation, sexe) VALUES (:nom, :prenom, :email, :telephone, :date_rdv, :heure_rdv, :type_consultation, :sexe)");
        $stmt->execute([':nom' => $nom, ':prenom' => $prenom, ':email' => $email, ':telephone' => $telephone, ':date_rdv' => $date_rdv, ':heure_rdv' => $heure_rdv, ':type_consultation' => $type_consultation, ':sexe' => $sexe]);
        $succes = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Insertion Rendez-vous - QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 650px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #5cb85c; }
        label { font-weight: bold; display: block; margin-top: 12px; }
        input, select { width: 100%; padding: 9px; border-radius: 5px; border: 1px solid #ccc; margin-top: 4px; box-sizing: border-box; }
        .err { color: red; font-size: 13px; }
        .succes-msg { background: #e6f4ea; color: green; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        button[type="submit"] { margin-top: 20px; padding: 10px 25px; background: #5cb85c; color: white; border: none; border-radius: 6px; cursor: pointer; width: 100%; }
        button[type="submit"]:hover { background: #449d44; }
        .nav-links a { margin-right: 15px; color: #5cb85c; text-decoration: none; font-weight: bold; }
        .form-row { display: flex; gap: 15px; }
        .form-row > div { flex: 1; }
    </style>
</head>
<body>
<div class="container">
    <div class="nav-links">
        <a href="insertion_rendezvous.php">➕ Ajouter</a>
        <a href="recherche_rendezvous.php">🔍 Rechercher</a>
        <a href="modification_rendezvous.php">✏️ Modifier</a>
        <a href="suppression_rendezvous.php">🗑️ Supprimer</a>
        <a href="../pages/rendezvous.html">← Retour</a>
    </div>
    <h2>➕ Ajouter un rendez-vous</h2>
    <?php if ($succes): ?><div class="succes-msg">✅ Rendez-vous ajouté ! <a href="recherche_rendezvous.php">Voir tous</a></div><?php endif; ?>
    <form method="POST">
        <div class="form-row">
            <div>
                <label>Nom :</label>
                <input type="text" name="nom" placeholder="Ex: Ben Ali" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                <?php if (isset($erreurs['nom'])): ?><span class="err">⚠ <?= $erreurs['nom'] ?></span><?php endif; ?>
            </div>
            <div>
                <label>Prénom :</label>
                <input type="text" name="prenom" placeholder="Ex: Sami" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                <?php if (isset($erreurs['prenom'])): ?><span class="err">⚠ <?= $erreurs['prenom'] ?></span><?php endif; ?>
            </div>
        </div>
        <label>Email :</label>
        <input type="email" name="email" placeholder="Ex: patient@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        <?php if (isset($erreurs['email'])): ?><span class="err">⚠ <?= $erreurs['email'] ?></span><?php endif; ?>
        <label>Téléphone :</label>
        <input type="text" name="telephone" placeholder="Ex: +21698765432" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
        <?php if (isset($erreurs['telephone'])): ?><span class="err">⚠ <?= $erreurs['telephone'] ?></span><?php endif; ?>
        <div class="form-row">
            <div>
                <label>Date :</label>
                <input type="date" name="date_rdv" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['date_rdv'] ?? '') ?>">
                <?php if (isset($erreurs['date_rdv'])): ?><span class="err">⚠ <?= $erreurs['date_rdv'] ?></span><?php endif; ?>
            </div>
            <div>
                <label>Heure :</label>
                <input type="time" name="heure_rdv" value="<?= htmlspecialchars($_POST['heure_rdv'] ?? '') ?>">
                <?php if (isset($erreurs['heure_rdv'])): ?><span class="err">⚠ <?= $erreurs['heure_rdv'] ?></span><?php endif; ?>
            </div>
        </div>
        <label>Type de consultation :</label>
        <select name="type_consultation">
            <option value="">-- Choisir --</option>
            <option value="consultation" <?= (($_POST['type_consultation'] ?? '') === 'consultation') ? 'selected' : '' ?>>Consultation</option>
            <option value="urgence"      <?= (($_POST['type_consultation'] ?? '') === 'urgence')      ? 'selected' : '' ?>>Urgence</option>
        </select>
        <?php if (isset($erreurs['type_consultation'])): ?><span class="err">⚠ <?= $erreurs['type_consultation'] ?></span><?php endif; ?>
        <label>Sexe :</label>
        <select name="sexe">
            <option value="">-- Choisir --</option>
            <option value="homme" <?= (($_POST['sexe'] ?? '') === 'homme') ? 'selected' : '' ?>>Homme</option>
            <option value="femme" <?= (($_POST['sexe'] ?? '') === 'femme') ? 'selected' : '' ?>>Femme</option>
        </select>
        <?php if (isset($erreurs['sexe'])): ?><span class="err">⚠ <?= $erreurs['sexe'] ?></span><?php endif; ?>
        <button type="submit">Enregistrer</button>
    </form>
</div>
</body>
</html>
