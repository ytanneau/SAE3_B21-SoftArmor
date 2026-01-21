<?php
    define('HOME_GIT', '../../../../');
    define('HOME_SITE', '../../../');
 
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

    // appel des fichiers de configuration et fonctions
    require_once HOME_GIT . ".config.php";
    include HOME_GIT . "fonction_categorie.php";
    include HOME_GIT . "fonction_produit.php";

    $promotion = $_GET['promotion'];
    $banniere = $_GET['banniere'];
    $produit = $_GET['produit'];
    unlink_image_promotion($promotion);
    delete_image($banniere);

    header("Location:index.php?produit=". $produit . "&idPromo=" . $promotion);
    exit();
?>