<?php
// Fichier db.php : Connexion à la base de données

$servername = "localhost";
$username = "root";
$password = "";
$database = "classes";

try {
    // Créer une connexion PDO
    $conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
    
    // Configurer PDO pour afficher les erreurs sous forme d'exceptions
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si une erreur survient, afficher un message et arrêter le script
    die("Connection failed: " . $e->getMessage());
}
?>
