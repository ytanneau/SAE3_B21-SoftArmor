<?php

define('HOME_SITE', '../');
define('HOME_GIT', '../../');


// Rediriger l'utilisateur si la 2FA est déjà activée sur son compte
if (!isset($_SESSION)) {
    session_start();
}

// Empêcher les visiteurs (non-connectés) d'accéder à la page
if (!isset($_SESSION['logged_in'])) {
    header('location: ' . HOME_SITE);
    exit;
}

require HOME_GIT . 'fonction_2FA.php';
require HOME_GIT . 'vendor/autoload.php';
use OTPHP\TOTP;

$accueil = isset($_SESSION['raison_sociale']) ? HOME_SITE . "vendeur/accueil" : HOME_SITE;
$erreur = "";

// si l'user a attendu le temps qu'il fallait (après avoir lamentablement échoué plusieurs fois)
// alors on reset le nombre de tentatives
if (isset($_SESSION['temps_attente_connexion']) && $_SESSION['temps_attente_connexion'] <= time()) {
    unset($_SESSION['temps_attente_connexion']);
    unset($_SESSION['nb_tentatives_connexion']);
}

// s'il vient juste d'arriver sur la page, ou s'il vient 
if (!isset($_SESSION['nb_tentatives_connexion'])) $_SESSION['nb_tentatives_connexion'] = 10;

// Après soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codePIN = htmlentities(trim($_POST['codePIN']) ?? '');
    $otp = TOTP::createFromSecret(get_clef_2FA($_SESSION['id_compte']));

    $erreur = check_code_PIN($codePIN);

    // si le user a encore des tentatives
    if (empty($erreur) && $_SESSION['nb_tentatives_connexion'] > 0) {
    
        if ($otp->verify($codePIN)) {
            // Si le code PIN est valide, alors on retire notre variable temporaire
            unset($_SESSION['nb_tentatives_connexion']);
            
            // et il est login
            $_SESSION['logged_in'] = true;

            // Si on se connecte à un compte client
            if (!isset($_SESSION['raison_sociale'])) {
                // Transférer le panier visiteur
                require HOME_GIT . "fonction_panier.php";
                transferer_panier_visiteur_compte($_SESSION['id_compte']);

                // Si le visiteur était en train de consulter le panier ou la page d'un produit, l'y rediriger
                if (isset($_GET['produit'])) {
                    if ($_GET['produit'] == 'panier') {
                        $page = HOME_SITE . 'panier';
                    } else {
                        $page = HOME_SITE . 'produit?produit=' . $_GET['produit'];
                    }
        
                    header('Location: ' . HOME_SITE . $page);
                    exit;
                }
            }

            // Rediriger par défaut à l'accueil client ou vendeur
            header('Location: ' . $accueil);

        } else {
            // si l'user a lamentablement échoué pour le code PIN
            $_SESSION['nb_tentatives_connexion']--;

            $erreur = "Code PIN incorrect";
        }

    }

    // si l'user a lamentablement échoué trop de fois, on le fait attendre quelques sec avant de réessayer (20 sec)
    if ($_SESSION['nb_tentatives_connexion'] == 0 && (!isset($_SESSION['temps_attente_connexion']))) {
        $_SESSION['temps_attente_connexion'] = time() + 20;
    }
}

$vendeur = isset($_SESSION['raison_sociale']);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include HOME_SITE . 'link_head.php' ?>
    <title>Alizon - 2FA</title>
</head>
<body id="connect_<?=$vendeur ? 'vendeur' : 'client'?>">
    <main>
        <a href="<?= HOME_SITE ?>">
            <img src="<?= HOME_SITE ?>image/Alizon_<?=$vendeur ? 'vendeur_noir' : 'noir'?>.png" alt="Logo alizon" title="Logo alizon">
        </a>
        <h2>S'identifier</h2>
        <form action="" method="post">
            <label>Ouvrez votre application de double authentification, puis entrez le code PIN affiché pour vous connecter</label>
            <label for="codePIN">Code PIN</label>
            <input type="number" id="codePIN" name="codePIN" class="champ">
            <p class="error"><?= $erreur ?></p>

            <?php if ($_SESSION['nb_tentatives_connexion'] <= 0) {?>
                <p class="error">Nombre de tentatives dépassé, attendez <span id="temps"><?=$_SESSION['temps_attente_connexion']?></span> secondes avant de réessayer</p>
            <?php } else { ?>
                <input type="submit" value="Se connecter" class="bouton">
            <?php } ?>
            
        </form>
        <p style="text-align:center">Clef perdue ? <a href="mailto:service@alizon.bzh">Contactez le service client</a></p>
    </main>
</body>
<script>
    <?php if (isset($_SESSION['temps_attente_connexion'])) { ?>
        let tempsRestant = <?=$_SESSION['temps_attente_connexion'] - time()?>;
        document.getElementById("temps").innerHTML = tempsRestant;

        let idInterval = setInterval(() => {
            tempsRestant--;
            document.getElementById("temps").innerHTML = tempsRestant;

            if (tempsRestant <= 0) {
                clearInterval(idInterval);
                window.location.href = "";
            }
        }, 1000);
    <?php } ?>
</script>
</html>