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



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <?php include HOME_SITE . 'link_head.php' ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon panier</title>
</head>
<body>
    <?php include HOME_SITE . 'header.php' ?>

    <?= "Bienvenue dans votre panier " . $_SESSION['pseudo']; ?>
</body>
</html>