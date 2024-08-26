<?php

include "./_db.php";

class User {
    private $id;

    public $login;
    public $password;  // Assurez-vous d'avoir cet attribut pour stocker le mot de passe
    public $email;
    public $firstname;
    public $lastname;
    
    public function __construct() {
        $this->id = null;
        $this->login = "";
        $this->password = ""; // Initialiser le mot de passe
        $this->email = "";
        $this->firstname = "";
        $this->lastname = "";
    }

    // Méthode pour l'enregistrement
    public function register($conn) {
        $query = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            die("Erreur lors de la préparation de la requête : " . $conn->error);
        }

        // Hacher le mot de passe
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);

        // Liaison des paramètres
        $stmt->bind_param("sssss", $this->login, $hashed_password, $this->email, $this->firstname, $this->lastname);

        if ($stmt->execute()) {
            // Assigner l'ID généré à l'objet après l'exécution de la requête
            $this->id = $conn->insert_id;
            // Retourner les informations de l'utilisateur nouvellement créé
            return $this->getAllInfos($conn);
        } else {
            return false;
        }
    }
    
    // Méthode pour la connexion
    public function connect($conn, $login, $password) {
        $query = "SELECT * FROM utilisateurs WHERE login = ?";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            die("Erreur lors de la préparation de la requête : " . $conn->error);
        }

        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return false;
        }

        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {
            $this->id = $user["id"];
            $this->login = $user["login"];
            $this->email = $user["email"];
            $this->firstname = $user["firstname"];
            $this->lastname = $user["lastname"];
            return true;
        } else {
            return false;
        }
    }

    // Méthode pour la déconnexion
    public function disconnect() {
        $this->id = null;
        $this->login = "";
        $this->password = ""; // Réinitialiser le mot de passe
        $this->email = "";
        $this->firstname = "";
        $this->lastname = "";
    }

    // Méthode pour supprimer le compte utilisateur
    public function delete($conn) {
        if ($this->id !== null) {
            $query = "DELETE FROM utilisateurs WHERE id = ?";
            $stmt = $conn->prepare($query);

            if ($stmt === false) {
                die("Erreur lors de la préparation de la requête : " . $conn->error);
            }

            $stmt->bind_param("i", $this->id);

            if ($stmt->execute()) {
                $this->disconnect(); // Déconnecter l'utilisateur après la suppression
                return true;
            }
        }
        return false;
    }

    // Méthode pour obtenir toutes les informations de l'utilisateur
    public function getAllInfos($conn) {
        $query = "SELECT * FROM utilisateurs WHERE id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            die("Erreur lors de la préparation de la requête : " . $conn->error);
        }

        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return false;
        }

        return $result->fetch_assoc();
    }
}

?>
