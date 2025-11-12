<?php

if (!isset($_SESSION)) {
    session_start();
}
    define('HOME_GIT', '../../');

// Si l'utilisateur est déjà connecté

if ($_POST != null){
    require_once (HOME_GIT . 'fonction_compte.php');
    $erreurs = connect_compte($_POST['email'], $_POST['mdp'], 'vendeur', HOME_GIT);
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
    <title>Alizon Vendeur - Connexion</title>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="<?= HOME_GIT . "html/style.css" ?>">
</head>
<body id="connect_vendeur">
    <main>
<?php
    if (isset($erreurs['fatal'])){
?>
        <h1 class="fatale">Désolé nous rencontrons des problèmes serveur</h1>
<?php
    }
    else {
?>
        <img src="" alt="">
        <a href="../">
            <img src="<?=HOME_GIT?>html/image/Alizon_vendeur_noir.png" alt="logo alizon" title="logo alizon">
        </a>
        <h2>S’identifier</h2>

        <form action="" method="post">
<?php
    if (isset($erreurs['connect'])){
?>
        <h3 class="error"><?=$erreurs['connect']?></h1>
<?php
    }
?>
                <!-- Adresse e-mail -->
            <br>
            <label for="email">Email</label>
            <input type="email"
                name="email"
                id="email"
                value="<?php if (isset($_POST['email'])) echo htmlentities($_POST['email'])?>"
                required
                class="champ">
<?php
    if (isset($erreurs['email'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['email']?>
            </p>
<?php
    }
?>

                <!-- Mot de passe -->
            <label for="mdp">Mot de passe</label>
            <input type="password" 
                name="mdp"
                id="mdp"
                required
                class="champ">
<?php
    if (isset($erreurs['mpd'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['mdp']?>
            </p>
<?php
    }
?>
            
            <input type="submit" value="Se connecter" class="bouton">            
        </form>
        <p>Pas de compte ? <a href="inscription/">S'inscrire</a></p>
<?php
    }
?>
    </main>
</body>
</html>