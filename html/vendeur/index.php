<?php

if (!isset($_SESSION)) {
    session_start();
}

define('HOME_GIT', '../../');
define('HOME_SITE', '../');

// Si l'utilisateur est déjà connecté

if ($_POST != null){
    require_once (HOME_GIT . 'fonction_compte.php');
    $erreurs = connect_compte($_POST['email'], $_POST['mdp'], 'vendeur', HOME_GIT);
}

// Si connecté en vendeur, rediriger vers le stock, si connecté en client, rediriger vers l'accueil
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header(isset($_SESSION['raison_sociale']) ? 'location: stock' : 'location: ' . HOME_SITE);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include HOME_SITE . 'link_head.php'; ?>
    <title>Alizon - Connexion</title>
</head>
<body id="connect_vendeur">
    <main>
<?php
    if (isset($erreurs['fatal'])){
?>
        <h1 class="fatale">Désolé, nous rencontrons des problèmes serveur</h1>
<?php
    }
    else {
?>
        <img src="" alt="">
        <a href="./">
            <img src="<?=HOME_SITE?>image/Alizon_vendeur_noir.png" alt="logo alizon" title="logo alizon">
        </a>
        <h2>S’identifier</h2>

        <form action="" method="post">
                <!-- Adresse e-mail -->
            <label for="email">Email</label>
            <input type="text"
                name="email"
                id="email"
                value="<?php if (isset($_POST['email'])) echo htmlentities($_POST['email'])?>"
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
                class="champ">
<?php
    if (isset($erreurs['mdp'])){
?>
            <p class="error">
                <?="Erreur : ".$erreurs['mdp']?>
            </p>
<?php
    }
?>      
        <?php if (isset($erreurs['connecte'])) { ?>
            <p class="error"><?=$erreurs['connecte']?></p>
        <?php } ?>
            
        <input type="submit" value="Se connecter" class="bouton">        
        </form>
        <p style="text-align:center;">Pas de compte ? <a href="inscription/">S'inscrire</a>
        <br>
        <a href="../">Retourner au côté client</a></p>
<?php
    }
?>
    </main>
</body>
</html>