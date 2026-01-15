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

    $id_image = $_GET['idImage'];
    $id_produit = $_GET['idProduit'];
    unlink_image_produit($id_image,$id_produit);
    delete_image($id_image);

    header("Location:index.php?produit=" . $id_produit);
    exit();
?>