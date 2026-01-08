<?php

// Inclusion du fichier de configuration
define('HOME_GIT', '../../');
define('HOME_SITE', '../');

if (!isset($_SESSION)) {
    session_start();

    $recherche = trim(htmlentities($_GET['recherche'] ?? ''));

    if (empty($recherche)) {
        header('location: ' . HOME_SITE);
        die();
    }

    if (isset($_SESSION['raison_sociale'])){
        header('location: /vendeur/stock/');
        die();
    }
}

require_once (HOME_GIT . '.config.php');
require_once (HOME_GIT . 'fonction_avis.php');
require_once (HOME_GIT . 'fonction_produit.php');
require_once (HOME_GIT . 'fonction_global.php');
require_once (HOME_GIT . 'fonction_panier.php');
require_once (HOME_GIT . 'fonction_recherche.php');

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

<script type="text/javascript">
    // Liste des filtres existants

    const isOver10 = (produit) => {
        return produit.prix >= 10;
    }

    const isBelow20 = (produit) => {
        return produit.prix <= 20;
    }

    // ...

    // Récupérer tous les produits dans un objet JSON
    async function getProduitsJSON() {
        const reponse = await fetch("http://10.253.5.107/recherche/produits.php");
        const produits = await reponse.json();
        return produits;
    }

    // Afficher la liste des produits filtrés
    function filterAndShow(produits) {
        console.log(
            produits.filter(isOver10)
        );
    }


    getProduitsJSON().then(produits => {
        filterAndShow(produits);
    });
</script>

</html>
