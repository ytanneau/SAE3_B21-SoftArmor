<?php

if (!isset($_SESSION)) {
    session_start();
}

define('HOME_GIT', '../../../');
define('HOME_SITE', '../../');

// Si l'utilisateur est déjà connecté

if ($_POST != null){
    require_once (HOME_GIT . 'fonction_compte.php');
    $erreurs = connect_compte($_POST['email'], $_POST['mdp'], 'client', HOME_GIT);
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('location: ' . HOME_SITE);
    exit;
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon Connexion</title>
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link rel="stylesheet" href="<?=HOME_SITE?>style.css">
</head>
<body id="connect_client">
    <main>
        <a href="../">
            <img src="<?=HOME_SITE?>image/Alizon_noir.png" alt="logo alizon" title="logo alizon">
        </a>

        <h2>S’identifier</h2>

        <form action="" method="post">

            <!-- Adresse e-mail -->
            <br>
            <label for="email">Email</label>
            <input type="text"
                name="email"
                id="email"
                value="<?php if (isset($_POST['email'])) echo $_POST['email']?>"
                class="champ">

            <!-- Message d'erreur pour l'email -->
            <p class="error">
                <?php
                    if (isset($erreurs['email'])) {
                        $message = $erreurs['email'];
                        
                        if ($erreurs['email'] === FORMAT) {
                            $message .= ". Exemple : xyz@domaine.fr"; 
                        }
                    }
                ?>
            </p>

            <!-- Mot de passe -->
            <label for="mdp">Mot de passe</label>
            <input type="password" 
                name="mdp"
                id="mdp"
                value=""
                class="champ">
            
            <!-- Message d'erreur pour le MDP -->
            <p class="error">
                <?php 
                    if (isset($erreurs['mdp']) && $erreurs['mdp'] === VIDE) { 
                        echo $erreurs['mdp']; 
                    } 
                ?>
            </p>
                
            <!-- Message d'erreur en cas d'identifiants invalides -->
            <p class="error">
                <?php
                    $pas_erreur_format = isset($erreurs['connecte']);
                    $erreur_email = isset($erreurs['email']);
                    $mdp_incorrect_non_vide = (isset($erreurs['mdp']) && $erreurs['mdp'] !== VIDE);

                    // Si aucune erreur de format mais identifiants incorrects OU erreur de format de mot de passe (autre que vide)
                    if ($pas_erreur_format || (!$erreur_email && $mdp_incorrect_non_vide)) { 
                        echo CONNECTE_PAS; 
                    } 
                ?>
            </p>
            
            <input type="submit" value="Se connecter" class="bouton"> 
        </form>
        <p>Pas de compte ? <a href="../inscription/">S'inscrire</a></p>
    </main>

    
</body>
</html>