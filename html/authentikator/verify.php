<?php
// Fichier qui teste si le code PIN envoyé est le bon pour la double authentification
// Ce fichier écrit '0' si ce n'est pas bon, '1' si c'est bon

define('HOME_SITE', '../');
define('HOME_GIT', '../../');

$correcte = false;

require_once HOME_GIT . 'fonction_2FA.php';

// Rediriger l'utilisateur si la 2FA est déjà activée sur son compte
if (!isset($_SESSION)) {
    session_start();

    // Empêcher les visiteurs (non-connectés) d'accéder à la page
    // Empêcher les comptes avec 2FA d'accéder à la page
    if (!isset($_SESSION['logged_in']) || a_2FA($_SESSION['id_compte'])) {
        echo '0';
        exit;
    }
}

if (!isset($_GET['codePIN']) || strlen($_GET['codePIN']) != 6) {
    echo '0';
    exit;
}


require_once HOME_GIT . 'vendor/autoload.php';
use OTPHP\TOTP;

$otp = TOTP::createFromSecret($_GET['clef']);
if (verify_2FA($otp, $_GET['codePIN'])) {
    echo '1';
    $correcte = true;
} else {
    echo '0';
    exit;
}


// si le code PIN est correcte, alors on enregistre dans la BDD
if ($correcte) {
    activer_2FA($_SESSION['id_compte'], $_GET['clef']);
}