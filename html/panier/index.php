<?php

// Constantes
define('HOME_GIT', "../../");
define('HOME_SITE', '../');

// Redirige les utilisateurs non connectés et les vendeurs
if (!isset($_SESSION)) {
    session_start();

    if(isset($_SESSION['raison_sociale'])){
        header('location: ' . HOME_GIT . 'vendeur/stock/');
        exit;
    }

    if (!isset($_SESSION['logged_in'])) {
        header('location: ' . HOME_SITE);
        exit;
    }
}

echo "Bienvenue dans votre panier " . $_SESSION['pseudo'];