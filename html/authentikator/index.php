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

// Après soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codePIN = htmlentities(trim($_POST['codePIN']));
    $otp = TOTP::createFromSecret(get_clef_2FA($_SESSION['id_compte']));

    if ($otp->verify($codePIN)) {
        $_SESSION['logged_in'] = true;

        if (!isset($_SESSION['raison_sociale'])) {
            require "fonction_panier.php";
            transferer_panier_visiteur_compte($_SESSION['id_compte']);
        }

        header('location: ' . $accueil);
    }
}

//
//
// Redirections CLIENT (MANQUE VENDEUR)
//
//

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
        header('location: ' . $accueil);
    }
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon - 2FA</title>
</head>
<body id="inscription_client">
    <form action="" method="post">
        <label for="codePIN">Code PIN</label>
        <input type="number" id="codePIN" name="codePIN">
    </form>
</body>
</html>