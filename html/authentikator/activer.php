<?php

define('HOME_SITE', '../');
define('HOME_GIT', '../../');

//
// TO DO : rediriger si l'utilisateur a déjà activé la 2FA
//

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
    <input type="text" name="codePIN">
</body>
</html>