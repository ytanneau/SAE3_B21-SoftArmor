<?php

define("HOME_GIT", "../../../");
define("HOME_SITE", "../../");

if (!isset($_SESSION)) {
    session_start();
}

// Si je suis connecté mais pas en tant que vendeur, retour à l'accueil client
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && !isset($_SESSION['raison_sociale'])) {
    header('location: ' . HOME_SITE);
    exit;

// Sinon si je ne suis pas connecté, retour à la page connexion vendeur
} else if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
    header('location: ../');
    exit;
}

//permet d'utiliser le fichier config.php
require_once HOME_GIT . '.config.php';
require_once HOME_GIT . 'fonction_produit.php';



// si l'un des élément est vide on le renvoir sur le stock
if (!(isset($_GET['produit']) && isset($_GET['nb']))){
    header('location: .');
}

// si le vendeur possède pas le produit
if (vendeur_possede_produit($_SESSION['id_compte'], ($_GET['produit'])) == null){
    header('location: .');
}

//var_dump($_GET['nb']);
update_stock($_GET['produit'], $_GET['nb']);
header('location: .#'. htmlentities($_GET['produit']));

