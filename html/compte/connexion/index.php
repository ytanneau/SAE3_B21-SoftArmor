<?php


define('HOME_GIT', '../../../');
define('HOME_SITE', '../../');

if (!isset($_SESSION)) {
    session_start();
}

// Après envoi du formulaire, vérification des informations et connexion si valide
if ($_POST != null) {
    require_once (HOME_GIT . 'fonction_compte.php');
    require_once (HOME_GIT . 'fonction_2FA.php');
    $resultat = connect_compte($_POST['email'], $_POST['mdp'], 'client', HOME_GIT);

    // Toutes les informations sont correctes
    if ($resultat === true) {

        // Si pas de double authentification, connecter directement
        if (!a_2FA($_SESSION['id_compte'])) {
            $_SESSION['logged_in'] = true;
        } else {
            header('Location: ' . HOME_SITE . "authentikator/");
        }
    }
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if (isset($_GET['produit'])) {
        if ($_GET['produit'] == 'panier') {
            $page = '../../panier';
        } else {
            $page = '../../produit?produit=' . $_GET['produit'];
        }
        // Si l'utilisateur se connecte après avoir essayé d'acheter un produit sans se connecter, alors il est redirigé vers ce produit après connexion
        header('Location: ' . HOME_SITE . $page);
    } else {
        // Sinon, retour accueil
        header('location: ' . HOME_SITE);
    }
    exit;
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include HOME_SITE . 'link_head.php' ?>
    <title>Alizon - Connexion</title>
</head>
<body id="connect_client">
    <main>
        <a href="<?= HOME_SITE ?>">
            <img src="<?= HOME_SITE . 'image/Alizon_noir.png' ?>" alt="Logo alizon" title="Logo alizon">
        </a>

        <h2>S’identifier</h2>

        <form action="" method="post">

            <!-- Adresse e-mail -->
            <label for="email">Email</label>
            <input type="text"
                name="email"
                id="email"
                value="<?php if (isset($_POST['email'])) echo $_POST['email']?>"
                class="champ">

            <!-- Message d'erreur pour l'email -->
            <p class="error">
                <?php
                    if (isset($resultat['email'])) {
                        $message = $resultat['email'];
                        
                        if ($resultat['email'] === FORMAT) {
                            $message .= ". Exemple : xyz@domaine.fr"; 
                        }

                        echo $message;
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
                    if (isset($resultat['mdp']) && $resultat['mdp'] === VIDE) { 
                        echo $resultat['mdp']; 
                    } 
                ?>
            </p>
                
            <!-- Message d'erreur en cas d'identifiants invalides -->
            <p class="error">
                <?php
                    $pas_erreur_format = isset($resultat['connecte']);
                    $erreur_email = isset($resultat['email']);
                    $mdp_incorrect_non_vide = (isset($resultat['mdp']) && $resultat['mdp'] !== VIDE);

                    // Si aucune erreur de format mais identifiants incorrects OU erreur de format de mot de passe (autre que vide)
                    if ($pas_erreur_format || (!$erreur_email && $mdp_incorrect_non_vide)) { 
                        echo CONNECTE_PAS; 
                    } 
                ?>
            </p>
            <p><a href="../reinitialiser<?php if (isset($_GET['produit'])) echo "?produit=" . $_GET['produit']?>">Mot de passe oublié ?</a></p>
            
            <input type="submit" value="Se connecter" class="bouton">
        </form>
        <p style="text-align:center;">Pas de compte ? <a href="<?=HOME_SITE?>compte/inscription?produit=<?=$_GET['produit'] ?? ''?>">S'inscrire</a> 
        <br>
        <a href="<?=HOME_SITE?>vendeur/">Passez du coté vendeur</a>
        
        </p>
        </main>
    </body>
</html>