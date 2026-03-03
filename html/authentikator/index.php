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

// Après soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codePIN = htmlentities(trim($_POST['codePIN']) ?? '');
    $otp = TOTP::createFromSecret(get_clef_2FA($_SESSION['id_compte']));

    // Si le code PIN est valide
    $erreur = check_code_PIN($codePIN);

    if ($otp->verify($codePIN)) {
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
    }
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
        <p type="error"><?= $erreur ?></p>
    </form>
</body>
</html>