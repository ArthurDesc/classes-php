<?php

include "./db.php";

class User {
    private $id;
    private $password;   
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

    public function register(PDO $conn, string $password): bool {
        // Vérifiez si l'email existe déjà
        $checkEmailQuery = "SELECT COUNT(*) FROM utilisateurs WHERE email = ?";
        $stmt = $conn->prepare($checkEmailQuery);
        $stmt->execute([$this->email]);
        
        if ($stmt->fetchColumn() > 0) {
            // Email déjà utilisé
            return false;
        }
    
        // Hachage du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
        // Insertion de l'utilisateur
        $query = "INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        return $stmt->execute([$this->login, $hashedPassword, $this->email, $this->firstname, $this->lastname]);
    }
    
    public function connect($conn, $login, $password) {
        $query = "SELECT * FROM utilisateurs WHERE login = :login";
        $stmt = $conn->prepare($query);

        $stmt->execute([':login' => $login]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
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

    public function disconnect() {
        $this->id = null;
        $this->login = "";
        $this->email = "";
        $this->firstname = "";
        $this->lastname = "";
        $this->password = "";
    }

    public function delete($conn) {
        if ($this->id !== null) {
            $query = "DELETE FROM utilisateurs WHERE id = :id";
            $stmt = $conn->prepare($query);

            if ($stmt->execute([':id' => $this->id])) {
                $this->disconnect();
                return true;
            }
        }
        return false;
    }

    public function update($conn, $new_email, $new_firstname, $new_lastname, $new_login, $new_password) {
        if ($this->id !== null) {
            $query = "UPDATE utilisateurs SET login = :login, email = :email, firstname = :firstname, lastname = :lastname";
            $params = [
                ':login' => $new_login,
                ':email' => $new_email,
                ':firstname' => $new_firstname,
                ':lastname' => $new_lastname,
                ':id' => $this->id
            ];

            if ($new_password) {
                $query .= ", password = :password";
                $params[':password'] = password_hash($new_password, PASSWORD_DEFAULT);
            }

            $query .= " WHERE id = :id";
            $stmt = $conn->prepare($query);
    
            if ($stmt->execute($params)) {
                $this->login = $new_login;
                $this->email = $new_email;
                $this->firstname = $new_firstname;
                $this->lastname = $new_lastname;
                return true;
            }
        }
        return false;
    }

    public function isConnected() {
        return $this->id !== null;
    }
    
    public function getAllInfos($conn) {
        $query = "SELECT * FROM utilisateurs WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':id' => $this->id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Getters remain unchanged

    public function setId($id) {
        $this->id = $id;
    }

    public static function getAllUsers($conn) {
        $query = "SELECT * FROM utilisateurs";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];

        foreach ($result as $row) {
            $user = new User();
            $user->setId($row['id']);
            $user->login = $row['login'];
            $user->email = $row['email'];
            $user->firstname = $row['firstname'];
            $user->lastname = $row['lastname'];
            $users[] = $user;
        }

        return $users;
    }
    
    public function fetchDetails($conn) {
        $query = "SELECT email, firstname, lastname FROM utilisateurs WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':id' => $this->id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($row) {
            $this->email = $row['email'];
            $this->firstname = $row['firstname'];
            $this->lastname = $row['lastname'];
        }
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }
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


    
}