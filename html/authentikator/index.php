<?php

define('HOME_SITE', '../');
define('HOME_GIT', '../../');

require HOME_GIT . 'fonction_2FA.php';

// Rediriger l'utilisateur si la 2FA est déjà activée sur son compte
if (!isset($_SESSION)) {
    session_start();

    // Empêcher les visiteurs (non-connectés) d'accéder à la page
    if (!isset($_SESSION['logged_in'])) {
        header('location: ' . HOME_SITE);
        exit;
    }

    // Empêcher les comptes avec 2FA d'accéder à la page
    if (a_2FA($_SESSION['id_compte'])) {
        if (isset($_SESSION['raison_sociale'])){
            header('location: '. HOME_SITE .'vendeur/stock/');

        } else {
            header('location: ' . HOME_SITE);

        }

        exit;
    }
}


require HOME_GIT . 'vendor/autoload.php';
use OTPHP\TOTP;

if (!isset($_SESSION)) {
    session_start();
}

$accueil = isset($_SESSION['raison_sociale']) ? HOME_SITE . "vendeur/accueil" : HOME_SITE;

// Si on ne vient pas de la page de connexion, redirection vers l'accueil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codePIN = htmlentities(trim($_POST['codePIN']));
    $otp = TOTP::createFromSecret();
}

// $_SESSION['logged_in'] = true;
$_SESSION['id_compte'] = $resSQL['id_compte'];
$_SESSION['email'] = $email;

if ($typeCompte == 'vendeur'){
    $_SESSION['raison_sociale'] = $resSQL['raison_sociale'];
} else {
    $_SESSION['pseudo'] = $resSQL['pseudo'];
}
    
require "fonction_panier.php";
transferer_panier_visiteur_compte($resSQL['id_compte']);

// Redirections
/*
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
*/

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alizon - 2FA</title>
</head>
<body id="inscription_client">
    <img src="<?=$grCodeUri?>">
    <p>Clef: <?=$otp->getSecret()?></p>

    <label for="codePIN">Entrez le code PIN pour activer la double authentification</label>
    <input type="number" name="codePIN" id="codePIN">
    <p id="erreur" class="erreur"></p>
    <button id="valider">Valider</button>
</body>
<script>
    let nbTentative = 10;
    document.getElementById("valider").onclick = function() {
        xmlhttp = new XMLHttpRequest();
        xmlhttp.onload = function() {
            if (this.responseText == '0') {
                nbTentative -= 1;
                document.getElementById("erreur").innerHTML = "Code PIN incorrecte, " + nbTentative + " tentatives restantes";

            } else {
                document.getElementById("erreur").innerHTML = "Code PIN correcte";
            }

            if (nbTentative == 0) {
                document.getElementById("erreur").innerHTML = "Erreur, ";
            }
        }

        if (nbTentative > 0) {
            xmlhttp.open("GET", "verify.php?codePIN=" + document.getElementById("codePIN").value + "&clef=<?=$otp->getSecret()?>");
            xmlhttp.send();
        }
    };
</script>
</html>