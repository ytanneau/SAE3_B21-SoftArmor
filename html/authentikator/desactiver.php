<?php
define('HOME_GIT', "../../");
define('HOME_SITE', '../');


// Rediriger l'utilisateur si la 2FA n'est pas activée sur son compte
if (!isset($_SESSION)) {
    session_start();
}

require_once(HOME_GIT . "fonction_2FA.php");

// Empêcher les visiteurs (non-connectés) d'accéder à la page
// Empêcher les comptes sans 2FA d'accéder à la page
if (!isset($_SESSION['logged_in']) || !a_2FA($_SESSION['id_compte'])) {
    header('location:' . HOME_SITE);
    exit;
}

require_once(HOME_GIT . 'vendor/autoload.php');
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
    $codePIN = htmlentities(trim($_POST['codePIN']));
    $otp = TOTP::createFromSecret(get_clef_2FA($_SESSION['id_compte']));
    
    $erreur = check_code_PIN($codePIN);

    // si le user a encore des tentatives
    if (empty($erreur) && $_SESSION['nb_tentatives_connexion'] > 0) {

        if ($otp->verify($codePIN)) {
            // Si le code PIN est valide, alors on retire notre variable temporaire
            unset($_SESSION['nb_tentatives_connexion']);

            desactiver_2FA($_SESSION['id_compte']);
            header("Location:$accueil");

        } else {
            // si l'user a lamentablement échoué pour le code PIN
            $_SESSION['nb_tentatives_connexion']--;

            $erreur = "Code PIN incorrect";
        }

    } 
    
    // si l'user a lamentablement échou trop de fois, on le fait attendre quelques sec avant de réessayer (20 sec)
    if ($_SESSION['nb_tentatives_connexion'] == 0 && (!isset($_SESSION['temps_attente_connexion']))) {
        $_SESSION['temps_attente_connexion'] = time() + 20;
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include HOME_SITE . 'link_head.php' ?>
    <title>Alizon - 2FA</title>
</head>
<body id="inscription_client">
    <?php 
        include HOME_SITE . "header.php";
        include HOME_SITE . "toolbar_categories.php"; 
    ?>

    <main>
        <?php $chemin = isset($_SESSION['raison_sociale']) ? 'vendeur/compte/information_compte_vendeur' : 'compte/informations' ?>
        <a href="<?= HOME_SITE . $chemin ?>"><img src="../image/retour.svg"></a>

        <h1>Désactiver la double authentification</h1>

        <form action="" method="post">
            <h3>Ouvrez votre application de double authentification, et entrez le code PIN enregistré pour votre compte</h3>

            <label for="codePIN">Code PIN</label>
            <input type="number" id="codePIN" name="codePIN">
            <p type="error"><?= $erreur ?></p>

            <?php if ($_SESSION['nb_tentatives_connexion'] <= 0) {?>
                <p type="error">Nombre de tentatives dépassé, attendez <span id="temps"><?=$_SESSION['temps_attente_connexion']?></span> secondes avant de réessayer</p>
            <?php } else { ?>
                <input type="submit" value="Désactiver">
            <?php } ?>
    
            <h3>Clef perdue ?</h3> 

            <p>Veuillez contacter le service client à l'email <a href="mailto:service@alizon.bzh">service@alizon.bzh</a></p>
        </form>
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