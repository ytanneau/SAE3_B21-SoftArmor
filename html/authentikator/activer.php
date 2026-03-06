<?php

define('HOME_SITE', '../');
define('HOME_GIT', '../../');

require_once HOME_GIT . 'fonction_2FA.php';


// Rediriger l'utilisateur si la 2FA est déjà activée sur son compte
if (!isset($_SESSION)) {
    session_start();
}

// Empêcher les visiteurs (non-connectés) d'accéder à la page
if (!isset($_SESSION['logged_in'])) {
    header('location: ' . HOME_SITE);
    exit;
}

$vendeur = isset($_SESSION['raison_sociale']);

// Empêcher les comptes avec 2FA d'accéder à la page
if (a_2FA($_SESSION['id_compte'])) {
    if ($vendeur) {
        header('location: '. HOME_SITE .'vendeur/stock/');
    } else {
        header('location: ' . HOME_SITE);
    }

    exit;
}

$accueil = $vendeur ? 'vendeur/accueil' : '';
$header = $vendeur ? 'vendeur/header.php' : 'header.php';
$retour = $vendeur ? 'vendeur/compte/information_compte_vendeur' : 'compte/informations';

require_once HOME_GIT . 'vendor/autoload.php';
use OTPHP\TOTP;
use OTPHP\InternalClock;

$clock = new InternalClock();

$otp = TOTP::generate($clock);
$otp = $otp->withPeriod(60);

$otp = $otp->withLabel('Alizon - ' . ($_SESSION['pseudo'] ?? $_SESSION['raison_sociale']));
$grCodeUri = $otp->getQrCodeUri(
    'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=300x300&ecc=M',
    '[DATA]'
);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include HOME_SITE . 'link_head.php' ?>
    <title>Alizon - 2FA</title>
</head>

<body id="page_2fa">
    <?php 
        include HOME_SITE . $header;
        if (!$vendeur) include HOME_SITE . "toolbar_categories.php"; 
    ?>

    <main>
        <a href="<?= HOME_SITE . $retour ?>"><img src="../image/retour.svg"></a>

        <h1>Activer la double authentification</h1>
        <p>La double authentification permet de sécuriser votre compte. À chaque connexion, vous devrez entrer un code PIN affiché dans une application de double authentification.</p>
        
        <h2>Étape 1</h2>

        <h3>Depuis votre application de double authentification, scannez ce QRCode ou saisissez la clé</h3>
        <p><strong>Ce code ne sera affiché qu'une seule fois.</strong> Veuillez le conserver dans votre application, ou vous risquez de perdre votre compte.</p>
        
        <div id="qrCode">
            <img src="<?=$grCodeUri?>">

            <p id="cle_2fa">
                <?=$otp->getSecret()?>
            </p>
        </div>

        <h2>Étape 2</h2>
        
        <p>Entrez le <strong>code PIN à 6 chiffres</strong> affiché dans votre application pour activer la double authentification</p>
        
        <label for="codePIN">Code PIN</label>
        <input type="number" name="codePIN" id="inputPIN" hidden value="">

        <div id="codePIN">
            <?php
            for ($i=0; $i < 6; $i++) { 
                ?><input <?=$i == 0 ? 'autofocus' : ''?> type="number" id="codePIN<?=$i?>" class="PIN"  max="9" min="0"><?php
            }
            ?>
        </div>
        
        <p id="erreur" class="error"></p>
        
        <button id="valider" class="bouton">Valider</button>
    </main>
</body>

<script src="codePIN.js"></script>

<script>
    const NB_TENTATIVES_DEPART = 10;
    const TEMPS_INTERVALLE_ERREUR = 10; // temps en sec d'intervalle à attendre après trop de tentatives ratées

    let nbTentative = NB_TENTATIVES_DEPART;
    let tempsIntervalle = 0; 
    document.getElementById("valider").onclick = function() {
        xmlhttp = new XMLHttpRequest();
        xmlhttp.onload = function() {
            if (this.responseText == '1') {
                window.location.reload(); // refresh la page et donc va rediriger vers l'accueil
                
            } else {
                nbTentative--;
                document.getElementById("erreur").innerHTML = "Code PIN incorrect, " + nbTentative + " tentatives restantes";
            }

            if (nbTentative == 0) {
                tempsIntervalle = TEMPS_INTERVALLE_ERREUR;

                let idInterval = setInterval(() => {
                    tempsIntervalle--;
                    document.getElementById("erreur").innerHTML = "Trop de tentatives incorrectes. Veuillez réessayer dans " + tempsIntervalle + " secondes";

                    if (tempsIntervalle <= 0) {
                        document.getElementById("valider").style.display = "";
                        document.getElementById("erreur").innerHTML = "Veuillez réessayer";
                        nbTentative = NB_TENTATIVES_DEPART;
                        clearInterval(idInterval);
                    }
                }, 1000);

                document.getElementById("erreur").innerHTML = "Trop de tentatives incorrectes. Veuillez réessayer dans " + tempsIntervalle + " secondes";
                document.getElementById("valider").style.display = "none";
            }
        }

        console.log(document.getElementById("inputPIN").value);

        if (nbTentative > 0 && document.getElementById("inputPIN").value.length == 6) {
            xmlhttp.open("GET", "verify.php?codePIN=" + document.getElementById("inputPIN").value + "&clef=<?=$otp->getSecret()?>");
            xmlhttp.send();
        }
    };

    let inputHasFocus = false;
    
    document.getElementById("codePIN").addEventListener('focus', (e) => {inputHasFocus = true;});
    document.getElementById('codePIN').addEventListener('blur', (e) => {inputHasFocus = false;});

    document.addEventListener("keypress", function(e) {
        if (e.keyCode == 13 && !e.repeat && inputHasFocus) { // touche entrée
            document.getElementById("valider").click();
        }
    })
</script>

</html>