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

    <div class="filter">
        <form>
            <fieldset>
                <legend>Filtrer par prix</legend>

                <label for="zeroTo20">0 € à 20 €</label>
                <input type="checkbox" name="prix" id="zeroTo20" value="zeroTo20">

                <label for="twentyTo50">20 € à 50 €</label>
                <input type="checkbox" name="prix" id="twentyTo50" value="twentyTo50">

                <label for="fiftyTo100">50 € à 100 €</label>
                <input type="checkbox" name="prix" id="fiftyTo100" value="fiftyTo100">

                <label for="hundredTo300">100 € à 300 €</label>
                <input type="checkbox" name="prix" id="hundredTo300" value="hundredTo300">

                <label for="over300">Plus de 300 €</label>
                <input type="checkbox" name="prix" id="over300" value="over300">
            </fieldset>
        </form>
            
    </div>
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



    // Callable permettant de cumuler tous les filtres actifs
    const combineFilters = (...filters) => (item) => {
        return filters.map((filter) => filter(item)).every((x) => x === true);
    }

    // On met un Listener sur le div contenant toutes les checkbox pour détecter les changements

    // Récupérer tous les produits dans un objet JSON
    async function getProduitsJSON() {
        const reponse = await fetch("http://10.253.5.107/recherche/produits.php");
        const produits = await reponse.json();
        return produits;
    }

    console.log("Test");

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
