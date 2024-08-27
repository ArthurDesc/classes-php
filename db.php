<?php
// Fichier db.php : Connexion à la base de données

$servername = "localhost";
$username = "root";
$password = "";
$database = "classes";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $database);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
