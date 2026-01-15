<?php


define("HOME_GIT", "../../");
define("HOME_SITE", "../");

if (!isset($_SESSION)) {
    session_start();
}


// Si je suis connecté mais en tant que vendeur, retour à l'accueil client
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['raison_sociale'])) {
    
    header('location: ' . HOME_SITE);
    exit;
    
// Sinon si je ne suis pas connecté, retour à la page connexion
} else if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
    echo 2;
    header('location: ../connexion');
    exit;
}

//permet d'utiliser le fichier config.php
require_once HOME_GIT . '.config.php';
require_once HOME_GIT . 'fonction_panier.php';



// si l'un des élément est vide on le renvoi au panier
if (!(isset($_GET['produit']) && isset($_GET['nb']))){
    header('location: .');
}


update_quantite($_GET['produit'], $_GET['nb'], $_SESSION['id_compte']);
header('location: .');


