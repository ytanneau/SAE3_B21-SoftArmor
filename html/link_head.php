<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap" rel="stylesheet">

<link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>

<?php

// Si c'est côté client (donc pas de session, donc non connecté à vendeur)
if (session_status() !== PHP_SESSION_ACTIVE || strlen($_SESSION['raison_sociale'] ?? '') == 0) {?>
    <link rel="stylesheet" media="screen and (max-width: 1024px)" href="<?= HOME_SITE ?>style_tele.css"/>
    <link rel="stylesheet" media="screen and (min-width: 1025px)" href="<?= HOME_SITE . "style.css" ?>">
<?php } else { // sinon, si vendeur ?>
    <link rel="stylesheet" href="<?= HOME_SITE . "style.css" ?>">
<?php } ?>
<link rel="icon" type="image/png" href="<?=HOME_SITE?>image/logo_Alizon_bleu.png">