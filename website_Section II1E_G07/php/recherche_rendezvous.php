<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : recherche_rendezvous.php
    Rôle    : Recherche dans la table rendezvous selon plusieurs critères
              Utilise : query(), fetch(), fetchAll(), fetchObject()
*/

require_once 'connexion.php';

$resultats     = [];
$resultatObjet = null;
$recherche     = false;

// Nombre total via query() + fetch()
$total = $pdo->query("SELECT COUNT(*) AS total FROM rendezvous")->fetch()['total'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recherche         = true;
    $nom               = trim($_POST['nom']               ?? '');
    $email             = trim($_POST['email']             ?? '');
    $type_consultation = trim($_POST['type_consultation'] ?? '');
    $sexe              = trim($_POST['sexe']              ?? '');
    $date_rdv          = trim($_POST['date_rdv']          ?? '');
    $id_exact          = intval($_POST['id_exact']        ?? 0);

    // fetchObject() : recherche par ID exact
    if ($id_exact > 0) {
        $stmt = $pdo->prepare("SELECT * FROM rendezvous WHERE id = ?");
        $stmt->execute([$id_exact]);
        $resultatObjet = $stmt->fetchObject();
    }

    // fetchAll() : recherche multicritères positionnelle
    $conditions = ["1=1"];
    $params     = [];
    if ($nom   !== '') { $conditions[] = "(nom LIKE ? OR prenom LIKE ?)"; $params[] = "%$nom%"; $params[] = "%$nom%"; }
    if ($email !== '') { $conditions[] = "email LIKE ?";                  $params[] = "%$email%"; }
    if ($type_consultation !== '') { $conditions[] = "type_consultation = ?"; $params[] = $type_consultation; }
    if ($sexe  !== '') { $conditions[] = "sexe = ?";                      $params[] = $sexe; }
    if ($date_rdv !== '') { $conditions[] = "date_rdv = ?";               $params[] = $date_rdv; }

    $stmt = $pdo->prepare("SELECT * FROM rendezvous WHERE " . implode(" AND ", $conditions) . " ORDER BY date_rdv ASC, heure_rdv ASC");
    $stmt->execute($params);
    $resultats = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche Rendez-vous - QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1100px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #5cb85c; }
        .search-form { background: #f0faf0; padding: 20px; border-radius: 8px; margin-bottom: 25px; }
        .search-form label { font-weight: bold; display: block; margin-top: 10px; }
        .search-form input, .search-form select { width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc; margin-top: 4px; box-sizing: border-box; }
        .search-form button { margin-top: 15px; padding: 10px 25px; background: #5cb85c; color: white; border: none; border-radius: 6px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #5cb85c; color: white; padding: 10px; }
        td { padding: 9px; border: 1px solid #ddd; text-align: center; font-size: 13px; }
        tr:nth-child(even) { background: #f9f9f9; }
        .badge { padding: 3px 10px; border-radius: 10px; font-size: 12px; }
        .badge-consultation { background: #d4edda; color: #155724; }
        .badge-urgence      { background: #f8d7da; color: #721c24; }
        .badge-homme        { background: #cce5ff; color: #004085; }
        .badge-femme        { background: #f8d7e3; color: #6f1d3b; }
        .objet-box { background: #f0faf0; border: 1px solid #5cb85c; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .stats { background: #f0faf0; padding: 10px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; color: #5cb85c; }
        .nav-links a { margin-right: 15px; color: #5cb85c; text-decoration: none; font-weight: bold; }
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

    <h2>🔍 Recherche dans les Rendez-vous</h2>
    <div class="stats">📊 Total des rendez-vous : <?= $total ?></div>

    <div class="search-form">
        <form method="POST">
            <label>Recherche par ID exact (fetchObject) :</label>
            <input type="number" name="id_exact" min="1" placeholder="Ex: 1" value="<?= htmlspecialchars($_POST['id_exact'] ?? '') ?>">
            <label>Nom ou Prénom :</label>
            <input type="text" name="nom" placeholder="Ex: Ben Ali" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
            <label>Email :</label>
            <input type="text" name="email" placeholder="Ex: patient@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <label>Type de consultation :</label>
            <select name="type_consultation">
                <option value="">-- Tous --</option>
                <option value="consultation" <?= (($_POST['type_consultation'] ?? '') === 'consultation') ? 'selected' : '' ?>>Consultation</option>
                <option value="urgence"      <?= (($_POST['type_consultation'] ?? '') === 'urgence')      ? 'selected' : '' ?>>Urgence</option>
            </select>
            <label>Sexe :</label>
            <select name="sexe">
                <option value="">-- Tous --</option>
                <option value="homme" <?= (($_POST['sexe'] ?? '') === 'homme') ? 'selected' : '' ?>>Homme</option>
                <option value="femme" <?= (($_POST['sexe'] ?? '') === 'femme') ? 'selected' : '' ?>>Femme</option>
            </select>
            <label>Date du rendez-vous :</label>
            <input type="date" name="date_rdv" value="<?= htmlspecialchars($_POST['date_rdv'] ?? '') ?>">
            <button type="submit">Rechercher</button>
        </form>
    </div>

    <?php if ($recherche): ?>
        <?php if (($_POST['id_exact'] ?? 0) > 0): ?>
            <h3>Résultat par ID (fetchObject) :</h3>
            <?php if ($resultatObjet): ?>
                <div class="objet-box">
                    <p><strong>ID :</strong> <?= $resultatObjet->id ?> | <strong>Patient :</strong> <?= htmlspecialchars($resultatObjet->prenom) ?> <?= htmlspecialchars($resultatObjet->nom) ?></p>
                    <p><strong>Email :</strong> <?= htmlspecialchars($resultatObjet->email) ?> | <strong>Tél :</strong> <?= htmlspecialchars($resultatObjet->telephone) ?></p>
                    <p><strong>Date :</strong> <?= htmlspecialchars($resultatObjet->date_rdv) ?> à <?= htmlspecialchars($resultatObjet->heure_rdv) ?></p>
                    <p><strong>Type :</strong> <span class="badge badge-<?= $resultatObjet->type_consultation ?>"><?= ucfirst($resultatObjet->type_consultation) ?></span>
                       | <strong>Sexe :</strong> <span class="badge badge-<?= $resultatObjet->sexe ?>"><?= ucfirst($resultatObjet->sexe) ?></span></p>
                </div>
            <?php else: ?><p style="color:red;">Aucun rendez-vous trouvé avec cet ID.</p><?php endif; ?>
        <?php endif; ?>

        <h3>Résultats (<?= count($resultats) ?> trouvé(s)) :</h3>
        <?php if (empty($resultats)): ?>
            <p style="color:gray;">Aucun résultat.</p>
        <?php else: ?>
            <table>
                <thead><tr><th>#</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Téléphone</th><th>Date</th><th>Heure</th><th>Type</th><th>Sexe</th></tr></thead>
                <tbody>
                    <?php foreach ($resultats as $row): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['nom']) ?></td>
                            <td><?= htmlspecialchars($row['prenom']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['telephone']) ?></td>
                            <td><?= htmlspecialchars($row['date_rdv']) ?></td>
                            <td><?= htmlspecialchars($row['heure_rdv']) ?></td>
                            <td><span class="badge badge-<?= $row['type_consultation'] ?>"><?= ucfirst($row['type_consultation']) ?></span></td>
                            <td><span class="badge badge-<?= $row['sexe'] ?>"><?= ucfirst($row['sexe']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
