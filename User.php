<?php

include "./db.php";

class User {
    private $id;

    public $login;
    public $email;
    public $firstname;
    public $lastname;

    public function __construct() {
        $this->id = null;
        $this->login = "";
        $this->email = "";
        $this->firstname = "";
        $this->lastname = "";
    }

    // Méthode pour l'enregistrement
    public function register($conn, $password) {
        $query = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
    
        if ($stmt === false) {
            die("Erreur lors de la préparation de la requête : " . $conn->error);
        }
    
        // Hacher le mot de passe passé en paramètre
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        // Liaison des paramètres
        $stmt->bind_param("sssss", $this->login, $hashed_password, $this->email, $this->firstname, $this->lastname);
    
        if ($stmt->execute()) {
            // Assigner l'ID généré à l'objet après l'exécution de la requête
            $this->id = $conn->insert_id;
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

    // Méthode pour mettre à jour les informations utilisateur
    public function update($conn, $new_email, $new_firstname, $new_lastname) {
        if ($this->id !== null) {
            $query = "UPDATE utilisateurs SET email = ?, firstname = ?, lastname = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
    
            if ($stmt === false) {
                die("Erreur lors de la préparation de la requête : " . $conn->error);
            }
    
            // Lier les nouveaux paramètres à la requête
            $stmt->bind_param("sssi", $new_email, $new_firstname, $new_lastname, $this->id);
    
            if ($stmt->execute()) {
                // Mettre à jour les attributs de l'objet après une mise à jour réussie
                $this->email = $new_email;
                $this->firstname = $new_firstname;
                $this->lastname = $new_lastname;
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    // Vérifier si l'utilisateur est connecté
    public function isConnected() {
        return $this->id !== null;
    }
    
    // Obtenir toutes les informations utilisateur
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

    // Méthodes pour obtenir les informations utilisateur
    public function getLogin() {
        return $this->login;
    }
    public function getEmail() {
        return $this->email;
    }
    public function getFirstname() {
        return $this->firstname;
    }
    public function getLastname() {
        return $this->lastname;
    }
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }
}


?>
