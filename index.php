<?php
// Inclure les fichiers nécessaires
require_once 'db.php';
require_once 'User.php';

// Démarrer la session
session_start();

// Variables pour stocker les messages d'erreur
$login_error = "";
$registration_error = "";
$deletion_confirmation = "";
$deletion_success = "";
$deletion_error = "";

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Gestion de la connexion
    if (isset($_POST['login']) && isset($_POST['password'])) {
        $login = $_POST['login'];
        $password = $_POST['password'];

        $user = new User();

        if ($user->connect($conn, $login, $password)) {
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_login'] = $user->getLogin();
        } else {
            $login_error = "Login ou mot de passe incorrect.";
        }
    }

    // Gestion de l'inscription
    if (isset($_POST['register_login']) && isset($_POST['register_password']) &&
        isset($_POST['register_email']) && isset($_POST['register_firstname']) && isset($_POST['register_lastname'])) {
        
        $register_login = $_POST['register_login'];
        $register_password = $_POST['register_password'];
        $register_email = $_POST['register_email'];
        $register_firstname = $_POST['register_firstname'];
        $register_lastname = $_POST['register_lastname'];

        $user = new User();
        $user->login = $register_login;
        $user->password = $register_password; // Le mot de passe sera haché dans la méthode register
        $user->email = $register_email;
        $user->firstname = $register_firstname;
        $user->lastname = $register_lastname;

        if ($user->register($conn, $register_password)) {
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_login'] = $user->getLogin();
            header("Location: index.php");
            exit();
        } else {
            $registration_error = "Une erreur est survenue lors de l'inscription.";
        }
    }

    // Gestion de la confirmation de suppression
    if (isset($_POST['action']) && $_POST['action'] === 'confirm_delete') {
        $deletion_confirmation = "Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.";
    }

    // Gestion de la suppression du compte
    if (isset($_POST['action']) && $_POST['action'] === 'delete_account') {
        $user = new User();

        if (isset($_SESSION['user_id'])) {
            $user->setId($_SESSION['user_id']);

            if ($user->delete($conn)) {
                session_unset();
                session_destroy();
                $deletion_success = "Votre compte a été supprimé avec succès.";
                header("Location: index.php");
                exit();
            } else {
                $deletion_error = "Une erreur est survenue lors de la suppression de votre compte.";
            }
        } else {
            $deletion_error = "Vous devez être connecté pour supprimer votre compte.";
        }
    }

    // Déconnexion
    if (isset($_POST['action']) && $_POST['action'] === 'logout') {
        $user = new User();
        
        if (isset($_SESSION['user_id'])) {
            $user->setId($_SESSION['user_id']);
            $user->disconnect();
        }

        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }
}

$is_logged_in = isset($_SESSION['user_login']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page d'accueil</title>
</head>
<body>
    <h1>Page d'accueil</h1>

    <?php if ($is_logged_in): ?>
        <p>Bienvenue, <?php echo htmlspecialchars($_SESSION['user_login']); ?>!</p>

        <?php if (!empty($deletion_success)): ?>
            <p style="color: green;"><?php echo htmlspecialchars($deletion_success); ?></p>
        <?php endif; ?>

        <?php if (!empty($deletion_error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($deletion_error); ?></p>
        <?php endif; ?>

        <?php if (!empty($deletion_confirmation)): ?>
            <p><?php echo htmlspecialchars($deletion_confirmation); ?></p>
            <form action="index.php" method="post">
                <input type="hidden" name="action" value="delete_account">
                <input type="submit" value="Confirmer la suppression">
                <a href="index.php">Annuler</a>
            </form>
        <?php else: ?>
            <form action="index.php" method="post">
                <input type="hidden" name="action" value="logout">
                <input type="submit" value="Se déconnecter">
            </form>
            <form action="index.php" method="post">
                <input type="hidden" name="action" value="confirm_delete">
                <input type="submit" value="Supprimer mon compte">
            </form>
        <?php endif; ?>

    <?php else: ?>
        <h2>Connexion</h2>
        <form action="index.php" method="post">
            <label for="login">Login:</label>
            <input type="text" id="login" name="login" required><br><br>

            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required><br><br>

            <input type="submit" value="Se connecter">
        </form>

        <?php if (!empty($login_error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($login_error); ?></p>
        <?php endif; ?>

        <h2>Inscription</h2>
        <form action="index.php" method="post">
            <label for="register_login">Login:</label>
            <input type="text" id="register_login" name="register_login" required><br><br>

            <label for="register_password">Mot de passe:</label>
            <input type="password" id="register_password" name="register_password" required><br><br>

            <label for="register_email">Email:</label>
            <input type="email" id="register_email" name="register_email" required><br><br>

            <label for="register_firstname">Prénom:</label>
            <input type="text" id="register_firstname" name="register_firstname" required><br><br>

            <label for="register_lastname">Nom:</label>
            <input type="text" id="register_lastname" name="register_lastname" required><br><br>

            <input type="submit" value="S'inscrire">
        </form>

        <?php if (!empty($registration_error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($registration_error); ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <tr>
        <th>Id</th>
        <th>Login</th>
        <th>Email</th>
        <th>Prénom</th>
        <th>Nom</th>
        <th>Actions</th>
        
    </tr>
</body>
</html>
