<?php

if (!isset($_SESSION)) {
    session_start();
}
    define('HOME_GIT', '../../');

// Si l'utilisateur est déjà connecté

if ($_POST != null){
    require_once (HOME_GIT . 'fonction_compte.php');
    $res = connect_compte($_POST['email'], $_POST['mdp'], 'vendeur', HOME_GIT);
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('location: stock');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>
<body>
    <main>
        <form action="" method="post">
            <legend>Informations</legend>

                <!-- Adresse e-mail -->
            <br>
            <label for="email">Email</label>
            <input type="email"
                name="email"
                id="email"
                value="<?php if (isset($_POST['email'])) echo $_POST['email']?>"
                required>

                <!-- Mot de passe -->
            <label for="mdp">Mot de passe</label>
            <label for="mdp">Mot de passe</label>
            <input type="password" 
                name="mdp"
                id="mdp"
                value="<?php if (isset($_POST['mdp'])) echo $_POST['mdp']?>"
                required>
            
            <input type="submit" value="Se connecter">            
        </form>
        <p>Pas de compte ? <a href="../inscription/">S'inscrire</a></p>
    </main>
</body>
</html>