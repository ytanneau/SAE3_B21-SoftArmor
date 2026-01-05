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
    }
    // Sinon si je ne suis pas connecté, retour à la page connexion vendeur
    else if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
        header('location: ../');
        exit;
    }

    //permet d'utiliser le fichier config.php
    /*require_once HOME_GIT . '.config.php';
    require_once HOME_GIT . 'fonction_produit.php';*/
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php include HOME_SITE . 'link_head.php'; ?>
        <title>Alizon - Accueil vendeur</title>
    </head>
    <body>
        <?php include "../header.php" ?>
        <article class="liste_accueil_vendeur">
            <a href="../compte/information_compte_vendeur">Mon compte</a>
            <a href="../stock">Gestion de stock</a>
            <a href="../commande">Commandes</a>
            <a href="../avis">Avis</a>
        </article>
        <?php include HOME_SITE . "footer.php" ?>
    </body>
</html>