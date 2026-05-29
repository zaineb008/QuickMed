<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : traitement_avis.php
    Rôle    : Traitement du formulaire questionnaire.html
              - Reçoit les données POST
              - Valide côté serveur
              - Insère dans la table avis (MySQL)
              - Affiche tous les avis sous forme de tableau HTML
              - Utilise une classe Avis avec constructeur, getters, setters
*/

require_once 'connexion.php';

/* =====================================================
   1. CLASSE Avis
      Représente un enregistrement de la table avis
===================================================== */
class Avis {

    // Attributs privés
    private $id;
    private $nom;
    private $email;
    private $satisfait;
    private $services;
    private $note;
    private $commentaire;
    private $dateAvis;

    // Constructeur
    public function __construct($id, $nom, $email, $satisfait, $services, $note, $commentaire, $dateAvis) {
        $this->id          = $id;
        $this->nom         = $nom;
        $this->email       = $email;
        $this->satisfait   = $satisfait;
        $this->services    = $services;
        $this->note        = $note;
        $this->commentaire = $commentaire;
        $this->dateAvis    = $dateAvis;
    }

    // Getters
    public function getId()          { return $this->id; }
    public function getNom()         { return $this->nom; }
    public function getEmail()       { return $this->email; }
    public function getSatisfait()   { return $this->satisfait; }
    public function getServices()    { return $this->services; }
    public function getNote()        { return $this->note; }
    public function getCommentaire() { return $this->commentaire; }
    public function getDateAvis()    { return $this->dateAvis; }

    // Setters
    public function setNom($nom)               { $this->nom = $nom; }
    public function setEmail($email)           { $this->email = $email; }
    public function setSatisfait($satisfait)   { $this->satisfait = $satisfait; }
    public function setServices($services)     { $this->services = $services; }
    public function setNote($note)             { $this->note = $note; }
    public function setCommentaire($commentaire) { $this->commentaire = $commentaire; }

    // Méthode : afficher les étoiles
    public function afficherEtoiles() {
        $etoiles = '';
        for ($i = 1; $i <= 5; $i++) {
            $etoiles .= ($i <= $this->note) ? '★' : '☆';
        }
        return $etoiles;
    }
}


/* =====================================================
   2. VALIDATION ET INSERTION (si formulaire soumis)
===================================================== */

$erreurs  = [];
$succes   = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupérer et nettoyer les données
    $nom         = trim($_POST['nom']         ?? '');
    $email       = trim($_POST['email']       ?? '');
    $satisfait   = trim($_POST['satisfait']   ?? '');
    $services    = $_POST['services']         ?? [];
    $note        = intval($_POST['note']      ?? 0);
    $commentaire = trim($_POST['commentaire'] ?? '');

    // --- Validation PHP côté serveur ---

    // Nom : min 3 lettres, pas de chiffres
    if (strlen($nom) < 3 || !preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $nom)) {
        $erreurs['nom'] = "Nom invalide (min 3 lettres, pas de chiffres).";
    }

    // Email : format valide avec regex (comme dans le cours)
    $regexEmail = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    if (!preg_match($regexEmail, $email)) {
        $erreurs['email'] = "Adresse email invalide.";
    }

    // Satisfaction : obligatoire
    if (!in_array($satisfait, ['oui', 'non'])) {
        $erreurs['satisfait'] = "Veuillez indiquer votre satisfaction.";
    }

    // Services : au moins un coché
    $servicesValides = ['consultation', 'rdv', 'teleconsultation'];
    $services = array_filter($services, function($s) use ($servicesValides) {
        return in_array($s, $servicesValides);
    });
    if (empty($services)) {
        $erreurs['services'] = "Veuillez sélectionner au moins un service.";
    }

    // Note : entre 1 et 5
    if ($note < 1 || $note > 5) {
        $erreurs['note'] = "Veuillez donner une note entre 1 et 5.";
    }

    // Commentaire : min 10 caractères
    if (strlen($commentaire) < 10) {
        $erreurs['commentaire'] = "Le commentaire doit contenir au moins 10 caractères.";
    }

    // Si aucune erreur → insérer dans la BD
    if (empty($erreurs)) {
        $servicesStr = implode(',', $services);

        // Requête préparée nommée (prepare + execute nommé)
        $stmt = $pdo->prepare("
            INSERT INTO avis (nom, email, satisfait, services, note, commentaire)
            VALUES (:nom, :email, :satisfait, :services, :note, :commentaire)
        ");
        $stmt->execute([
            ':nom'         => $nom,
            ':email'       => $email,
            ':satisfait'   => $satisfait,
            ':services'    => $servicesStr,
            ':note'        => $note,
            ':commentaire' => $commentaire,
        ]);

        $succes = true;
    }
}


/* =====================================================
   3. RÉCUPÉRER TOUS LES AVIS depuis la BD
      Construire un tableau d'objets Avis
===================================================== */

// fetchAll() → récupère tous les enregistrements
$rows    = $pdo->query("SELECT * FROM avis ORDER BY date_avis DESC")->fetchAll();
$avisList = [];

foreach ($rows as $row) {
    $avisList[] = new Avis(
        $row['id'],
        $row['nom'],
        $row['email'],
        $row['satisfait'],
        $row['services'],
        $row['note'],
        $row['commentaire'],
        $row['date_avis']
    );
}


/* =====================================================
   4. FONCTION : Afficher le tableau HTML des avis
      Parcourt le tableau d'objets avec foreach
===================================================== */
function afficherTableauAvis(array $avisList): void {
    if (empty($avisList)) {
        echo "<p style='text-align:center; color:gray;'>Aucun avis pour le moment.</p>";
        return;
    }

    echo "<table border='1' cellpadding='10' cellspacing='0' style='width:100%; border-collapse:collapse;'>";
    echo "<thead style='background:#2c6ed5; color:white;'>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Satisfait</th>
                <th>Services</th>
                <th>Note</th>
                <th>Commentaire</th>
                <th>Date</th>
            </tr>
          </thead><tbody>";

    foreach ($avisList as $avis) {
        // Sélection : couleur selon satisfaction
        $couleur = ($avis->getSatisfait() === 'oui') ? '#e6f4ea' : '#fdecea';

        echo "<tr style='background:{$couleur};'>
                <td>{$avis->getId()}</td>
                <td>" . htmlspecialchars($avis->getNom()) . "</td>
                <td>" . htmlspecialchars($avis->getEmail()) . "</td>
                <td>" . ($avis->getSatisfait() === 'oui' ? '✅ Oui' : '❌ Non') . "</td>
                <td>" . htmlspecialchars($avis->getServices()) . "</td>
                <td style='color:gold; font-size:18px;'>" . $avis->afficherEtoiles() . "</td>
                <td>" . htmlspecialchars($avis->getCommentaire()) . "</td>
                <td>" . htmlspecialchars($avis->getDateAvis()) . "</td>
              </tr>";
    }

    echo "</tbody></table>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultat - Avis QuickMed</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 1000px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; }
        .succes-msg { background: #e6f4ea; color: green; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
        .erreur-msg { background: #fdecea; color: red; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
        h2 { color: #2c6ed5; }
        a.btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #2c6ed5; color: white; border-radius: 8px; text-decoration: none; }
    </style>
</head>
<body>
<div class="container">

    <h2>📋 Résultat de votre avis</h2>

    <?php if ($succes): ?>
        <div class="succes-msg">✅ Merci ! Votre avis a bien été enregistré.</div>
    <?php elseif (!empty($erreurs) && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="erreur-msg">
            <strong>⚠ Erreurs détectées :</strong>
            <ul>
                <?php foreach ($erreurs as $champ => $msg): ?>
                    <li><?= htmlspecialchars($msg) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <h3>Tous les avis enregistrés :</h3>
    <?php afficherTableauAvis($avisList); ?>

    <a class="btn" href="../pages/questionnaire.html">← Retour au questionnaire</a>
</div>
</body>
</html>
