<?php

// Inclusion du fichier de configuration
define('HOME_GIT', '../../');
define('HOME_SITE', '../');

if (!isset($_SESSION)) {
    session_start();

    $recherche = trim(htmlentities($_GET['recherche'] ?? ''));

    if (isset($_SESSION['raison_sociale'])){
        header('location: /vendeur/stock/');
        die();
    } else if (empty($recherche)) {
        header('location: ' . HOME_SITE);
        die();
    }
}

require_once (HOME_GIT . '.config.php');
require_once (HOME_GIT . 'fonction_avis.php');
require_once (HOME_GIT . 'fonction_produit.php');
require_once (HOME_GIT . 'fonction_global.php');
require_once (HOME_GIT . 'fonction_panier.php');

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include HOME_SITE . "link_head.php" ?>
    <title>Alizon - Recherche</title>
</head>
<body>
    <?php include HOME_SITE . "header.php"; ?>

    <h1>Résultats pour "<?= $recherche ?>"</h1>
</body>
</html>
