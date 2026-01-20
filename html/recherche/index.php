<?php

// Inclusion du fichier de configuration
define('HOME_GIT', '../../');
define('HOME_SITE', '../');

if (!isset($_SESSION)) {
    session_start();

    $recherche = trim($_GET['recherche']);

    if (empty($recherche)) {
        header('location: ' . HOME_SITE);
        die();
    }

    if (isset($_SESSION['raison_sociale'])){
        header('location: ' . HOME_SITE . '/vendeur/stock/');
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
<body data-page="search"> 
    <main>
        <?php include HOME_SITE . "header.php"; ?>
        <h1 id="results_for">Résultats pour "<?= $recherche ?>"</h1>

        <section class="results">
            <!-- Grille des résultats -->
            <ul id="results"></ul>
        </section>
        <aside class="triEtFiltre">
            <section class="filters">
                <form>
                    <fieldset id = "prixF">
                        <div>
                            <legend>Filtrer par prix</legend>
                        </div>
                        <div class="filtreSection">
                        <input type="radio" name="prix" id="zeroTo20" value="zeroTo20">
                        <label for="zeroTo20">0 € à 20 €</label>
                        </div>
                        <div class="filtreSection">
                        <input type="radio" name="prix" id="twentyTo50" value="twentyTo50">
                        <label for="twentyTo50">20 € à 50 €</label>
                        </div>
                        <div class="filtreSection">
                        <input type="radio" name="prix" id="fiftyTo100" value="fiftyTo100">
                        <label for="fiftyTo100">50 € à 100 €</label>
                        </div>
                        <div class="filtreSection">
                        <input type="radio" name="prix" id="hundredTo300" value="hundredTo300">
                        <label for="hundredTo300">100 € à 300 €</label>
                        </div>
                        <div class="filtreSection">
                        <input type="radio" name="prix" id="over300" value="over300">
                        <label for="over300">Plus de 300 €</label>
                        </div>
                        <div class="filtreSection">
                        <input type="checkbox" name="prom" id="prom" value="prom">
                        <label for="prom">Promotion</label>
                        </div>
                    </fieldset>
                </form>
            </section>
            <label for="tri">Trier par </label>
            <select id="tri" value ="triOption">
                <option value="nom_public" data-name ="ASC">Ordre alphabétique</option>
                <option value="note_moy" data-name ="DESC">Meilleurs avis</option>
                <option value="triPrix" data-name ="ASC">Prix croissants</option>
                <option value="triPrixCroi" data-name ="DESC">Prix décroissants</option>
            </select>
        </aside>
    </main>

    

    <?php include HOME_SITE . "footer.php" ?>
    <style>
          
            .filters {
                display : flex;
                flex-direction: column;
                margin-right: 88%;
                margin-top: 5%;
                margin-bottom: 4%;
            }

            .filters form{
                display : flex;
                flex-direction: column;
            }


            .prixF{
                display: flex;
                flex-direction: column;
            }

            
        </style>
</body>

</html>
