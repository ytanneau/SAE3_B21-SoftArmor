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

// Après soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codePIN = htmlentities(trim($_POST['codePIN']));
    $otp = TOTP::createFromSecret(get_clef_2FA($_SESSION['id_compte']));

    if ($otp->verify($codePIN)) {
        desactiver_2FA($_SESSION['id_compte']);
        header("Location:$accueil");
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
        <h1>Désactiver la double authentification</h1>

        <form action="" method="post">
            <h3>Ouvrez votre application de double authentification, et entrez le code PIN enregistré pour votre compte</h3>

            <label for="codePIN">Code PIN</label>
            <input type="number" id="codePIN" name="codePIN">
    
            <input type="submit" value="Désactiver">
    
            <h3>Clé perdue ?</h3> 
            
            <p>Veuillez contacter le service client à l'email <a href="mailto:service@alizon.bzh">service@alizon.bzh</a></p>
        </form>
    </main>
</body>
</html>