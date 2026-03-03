<?php
require '../../../vendor/autoload.php';
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
    <title>Document</title>
</head>
<body id="inscription_client">
    
</body>
</html>