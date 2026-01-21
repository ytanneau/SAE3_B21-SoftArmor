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
}

//permet d'utiliser le fichier config.php
require_once HOME_GIT . '.config.php';
require_once HOME_GIT . 'fonction_panier.php';



// si l'un des élément est vide on le renvoi au panier
if (!(isset($_GET['produit']) && isset($_GET['nb']))){
    header('location: .');
}

if (isset($_SESSION['id_compte'])) {
    update_quantite($_GET['produit'], $_GET['nb'], $_SESSION['id_compte']);

} else {
    update_quantite_panier_visiteur($_GET['produit'], $_GET['nb']);
}

header('location: .');


