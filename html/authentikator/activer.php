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

// Empêcher les comptes avec 2FA d'accéder à la page
if (a_2FA($_SESSION['id_compte'])) {
    if (isset($_SESSION['raison_sociale'])){
        header('location: '. HOME_SITE .'vendeur/stock/');
    } else {
        header('location: ' . HOME_SITE);
    }

    exit;
}

$accueil = isset($_SESSION['raison_sociale']) ? HOME_SITE . "vendeur/accueil" : HOME_SITE;

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

<body id="activer_2FA">
    <?php include HOME_SITE . "header.php" ?>
    <main>
        <h2>Scannez ce QRCode depuis une application de double authentification, ou entrez-y la clef</h2>
        <p>La double authentification permet de sécuriser son compte. À chaque fois que vous vous connecterez à votre compte, vous devrez entrer un code PIN affiché dans une application de double authentification.</p>
        <p>Veuillez garder cette clef enregistrée dans votre application.</p>
        <img src="<?=$grCodeUri?>">
        <p>Clef: <?=$otp->getSecret()?></p>

        <form>
            <label for="codePIN">Entrez ensuite le code PIN affiché pour activer la double authentification</label>
            <input type="number" name="codePIN" id="codePIN">
            <p id="erreur" class="erreur"></p>
            
            <button id="valider">Valider</button>
        </form>
    </main>
</body>

<script>
    const NB_TENTATIVES_DEPART = 10;
    const TEMPS_INTERVALLE_ERREUR = 10; // temps en sec d'intervalle à attendre après trop de tentatives ratées

    let nbTentative = NB_TENTATIVES_DEPART;
    let tempsIntervalle = 0; 
    document.getElementById("valider").onclick = function() {
        xmlhttp = new XMLHttpRequest();
        xmlhttp.onload = function() {
            if (this.responseText == '1') {
                document.getElementById("erreur").innerHTML = "Code PIN correct, vous pouvez aller à l'accueil";
                window.location.reload(); // refresh la page et donc va rediriger vers l'accueil
                
            } else {
                nbTentative--;
                document.getElementById("erreur").innerHTML = "Code PIN incorrect, " + nbTentative + " tentatives restantes";
            }

            if (nbTentative == 0) {
                tempsIntervalle = TEMPS_INTERVALLE_ERREUR;

                let idInterval = setInterval(() => {
                    tempsIntervalle--;
                    document.getElementById("erreur").innerHTML = "Vous avez trop de tentatives incorrectes ! Veuillez reessayer dans " + tempsIntervalle + " secondes";

                    if (tempsIntervalle <= 0) {
                        document.getElementById("valider").style.display = "";
                        document.getElementById("erreur").innerHTML = "Veuillez réessayer";
                        nbTentative = NB_TENTATIVES_DEPART;
                        clearInterval(idInterval);
                    }
                }, 1000);

                document.getElementById("erreur").innerHTML = "Vous avez trop de tentatives incorrectes ! Veuillez réessayer dans " + tempsIntervalle + " secondes";
                document.getElementById("valider").style.display = "none";
            }
        }

        if (nbTentative > 0) {
            xmlhttp.open("GET", "verify.php?codePIN=" + document.getElementById("codePIN").value + "&clef=<?=$otp->getSecret()?>");
            xmlhttp.send();
        }
    };
</script>
</html>