<?php

define('HOME_SITE', '../');
define('HOME_GIT', '../../');

require HOME_GIT . 'fonction_2FA.php';


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

require HOME_GIT . 'vendor/autoload.php';
use OTPHP\TOTP;
use OTPHP\InternalClock;

$clock = new InternalClock();

$otp = TOTP::generate($clock);
$otp = $otp->withPeriod(60);

$otp = $otp->withLabel('Alizon');
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
    <title>Alizon - Authentikator</title>
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
            if (this.responseText == '1') {
                document.getElementById("erreur").innerHTML = "Code PIN correcte";
                
            } else {
                nbTentative -= 1;
                document.getElementById("erreur").innerHTML = "Code PIN incorrecte, " + nbTentative + " tentatives restantes";
            }

            if (nbTentative == 0) {
                document.getElementById("erreur").innerHTML = "Erreur, activation de la double authentification refusée";
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