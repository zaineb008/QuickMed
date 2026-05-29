<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : recherche_contact.php
    Rôle    : Recherche dans la table contacts
              Utilise : query(), fetch(), fetchAll(), fetchObject()
*/

require_once 'connexion.php';

$resultats     = [];
$resultatObjet = null;
$recherche     = false;

// Nombre total via query() + fetch()
$total = $pdo->query("SELECT COUNT(*) AS total FROM contacts")->fetch()['total'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recherche = true;
    $nom       = trim($_POST['nom']      ?? '');
    $sujet     = trim($_POST['sujet']    ?? '');
    $id_exact  = intval($_POST['id_exact'] ?? 0);

    // fetchObject() : recherche par ID exact
    if ($id_exact > 0) {
        $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = :id");
        $stmt->execute([':id' => $id_exact]);
        $resultatObjet = $stmt->fetchObject();
    }

    // fetchAll() : recherche multicritères positionnelle
    $conditions = ["1=1"];
    $params     = [];
    if ($nom   !== '') { $conditions[] = "nom LIKE ?";   $params[] = "%$nom%"; }
    if ($sujet !== '') { $conditions[] = "sujet LIKE ?"; $params[] = "%$sujet%"; }

    $stmt = $pdo->prepare("SELECT * FROM contacts WHERE " . implode(" AND ", $conditions) . " ORDER BY date_envoi DESC");
    $stmt->execute($params);
    $resultats = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche Contacts - QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1000px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { color: #0066cc; }
        .search-form { background: #f0f4ff; padding: 20px; border-radius: 8px; margin-bottom: 25px; }
        .search-form label { font-weight: bold; display: block; margin-top: 10px; }
        .search-form input { width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc; margin-top: 4px; box-sizing: border-box; }
        .search-form button { margin-top: 15px; padding: 10px 25px; background: #0066cc; color: white; border: none; border-radius: 6px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #0066cc; color: white; padding: 10px; }
        td { padding: 10px; border: 1px solid #ddd; }
        tr:nth-child(even) { background: #f9f9f9; }
        .stats { background: #e8f0fe; padding: 10px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; }
        .objet-box { background: #fffbe6; border: 1px solid #f0c040; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .nav-links a { margin-right: 15px; color: #0066cc; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <div class="nav-links">
        <a href="insertion_contact.php">➕ Ajouter</a>
        <a href="recherche_contact.php">🔍 Rechercher</a>
        <a href="modification_contact.php">✏️ Modifier</a>
        <a href="suppression_contact.php">🗑️ Supprimer</a>
        <a href="../pages/contact.html">← Retour</a>
    </div>

    <h2>🔍 Recherche dans les Messages de Contact</h2>
    <div class="stats">📊 Total des messages : <?= $total ?></div>

    <div class="search-form">
        <form method="POST">
            <label>Recherche par ID exact (fetchObject) :</label>
            <input type="number" name="id_exact" min="1" placeholder="Ex: 1" value="<?= htmlspecialchars($_POST['id_exact'] ?? '') ?>">
            <label>Nom de l'expéditeur :</label>
            <input type="text" name="nom" placeholder="Ex: Ahmed" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
            <label>Sujet :</label>
            <input type="text" name="sujet" placeholder="Ex: rendez-vous" value="<?= htmlspecialchars($_POST['sujet'] ?? '') ?>">
            <button type="submit">Rechercher</button>
        </form>
    </div>

    <?php if ($recherche): ?>

        <?php if (($_POST['id_exact'] ?? 0) > 0): ?>
            <h3>Résultat par ID (fetchObject) :</h3>
            <?php if ($resultatObjet): ?>
                <div class="objet-box">
                    <p><strong>ID :</strong> <?= $resultatObjet->id ?> | <strong>Nom :</strong> <?= htmlspecialchars($resultatObjet->nom) ?></p>
                    <p><strong>Email :</strong> <?= htmlspecialchars($resultatObjet->email) ?> | <strong>Tél :</strong> <?= htmlspecialchars($resultatObjet->telephone ?? '-') ?></p>
                    <p><strong>Sujet :</strong> <?= htmlspecialchars($resultatObjet->sujet) ?></p>
                    <p><strong>Message :</strong> <?= htmlspecialchars($resultatObjet->message) ?></p>
                    <p><strong>Date :</strong> <?= htmlspecialchars($resultatObjet->date_envoi) ?></p>
                </div>
            <?php else: ?>
                <p style="color:red;">Aucun message trouvé avec cet ID.</p>
            <?php endif; ?>
        <?php endif; ?>

        <h3>Résultats (<?= count($resultats) ?> trouvés) :</h3>
        <?php if (empty($resultats)): ?>
            <p style="color:gray;">Aucun résultat.</p>
        <?php else: ?>
            <table>
                <thead><tr><th>#</th><th>Nom</th><th>Email</th><th>Téléphone</th><th>Sujet</th><th>Message</th><th>Date</th></tr></thead>
                <tbody>
                    <?php foreach ($resultats as $c): ?>
                        <tr>
                            <td><?= $c['id'] ?></td>
                            <td><?= htmlspecialchars($c['nom']) ?></td>
                            <td><?= htmlspecialchars($c['email']) ?></td>
                            <td><?= htmlspecialchars($c['telephone'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($c['sujet']) ?></td>
                            <td><?= htmlspecialchars($c['message']) ?></td>
                            <td><?= htmlspecialchars($c['date_envoi']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
