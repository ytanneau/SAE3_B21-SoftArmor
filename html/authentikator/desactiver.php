<?php
define('HOME_GIT', "../../../");
define('HOME_SITE', '../../');

// Rediriger l'utilisateur si la 2FA n'est pas activée sur son compte
if (!isset($_SESSION)) {
    session_start();

    // Empêcher les visiteurs (non-connectés) d'accéder au site
    if (!isset($_SESSION['logged_in'])) {
        header('location: ../../');
        exit;
    }

    // Empêcher les comptes sans 2FA d'accéder à la page
    if (isset($_SESSION['raison_sociale'])){
        header('location: '.HOME_GIT.'vendeur/stock/');
        exit;
    }
}

?>