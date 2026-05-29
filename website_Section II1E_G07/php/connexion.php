<?php
/*
    Membres: Zeineb Mekki / Israa Trabelsi
    Fichier : connexion.php
    Rôle    : Connexion à la base de données MySQL via PDO
*/

$host     = 'localhost';
$dbname   = 'quickmed_db';
$user     = 'root';
$password = '';  // mot de passe XAMPP par défaut (vide)

try {
    // Créer la connexion PDO avec options
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // lancer une exception si erreur
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // retourner des tableaux associatifs
        ]
    );
} catch (PDOException $e) {
    // Afficher un message d'erreur propre et arrêter le script
    die("<p style='color:red;'>Erreur de connexion : " . $e->getMessage() . "</p>");
}
?>
