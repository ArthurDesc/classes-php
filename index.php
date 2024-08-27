<?php
// Inclure les fichiers nécessaires
require_once 'db.php';
require_once 'User.php';

// Démarrer la session
session_start();

// Vérifier si le formulaire de connexion a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login']) && isset($_POST['password'])) {
    // Récupérer les informations du formulaire
    $login = $_POST['login'];
    $password = $_POST['password'];

    // Créer une instance de User
    $user = new User();

    // Tenter de connecter l'utilisateur
    if ($user->connect($conn, $login, $password)) {
        // Connexion réussie : Stocker les informations de l'utilisateur dans la session
        $_SESSION['user_id'] = $user->getId(); // Vous pouvez également stocker d'autres informations si nécessaire
        $_SESSION['user_login'] = $user->getLogin();
    } else {
        // Connexion échouée
        $login_error = "Login ou mot de passe incorrect.";
    }
}

// Vérifier si l'utilisateur est connecté
$is_logged_in = isset($_SESSION['user_id']);
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
        <form action="index.php" method="post">
            <input type="hidden" name="action" value="logout">
            <input type="submit" value="Se déconnecter">
        </form>
    <?php else: ?>
        <h2>Connexion</h2>
        <form action="index.php" method="post">
            <label for="login">Login:</label>
            <input type="text" id="login" name="login" required><br><br>

            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required><br><br>

            <input type="submit" value="Se connecter">
        </form>

        <?php if (isset($login_error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($login_error); ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <?php
    // Déconnexion
    if (isset($_POST['action']) && $_POST['action'] === 'logout') {
        session_unset(); // Détruire toutes les variables de session
        session_destroy(); // Détruire la session
        header("Location: index.php"); // Rediriger vers la page d'accueil
        exit();
    }
    ?>
</body>
</html>
