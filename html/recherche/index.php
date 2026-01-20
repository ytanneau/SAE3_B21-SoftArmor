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
    <?php include HOME_SITE . "header.php"; ?>

    <main>
        <section class="filters">
            <form>
                <fieldset id = "prixF">
                    <legend>Filtrer par prix</legend>

                    <input type="radio" name="prix" id="zeroTo20" value="zeroTo20">
                    <label for="zeroTo20">0 € à 20 €</label>

                    <input type="radio" name="prix" id="twentyTo50" value="twentyTo50">
                    <label for="twentyTo50">20 € à 50 €</label>

                    <input type="radio" name="prix" id="fiftyTo100" value="fiftyTo100">
                    <label for="fiftyTo100">50 € à 100 €</label>

                    <input type="radio" name="prix" id="hundredTo300" value="hundredTo300">
                    <label for="hundredTo300">100 € à 300 €</label>

                    <input type="radio" name="prix" id="over300" value="over300">
                    <label for="over300">Plus de 300 €</label>

                    <input type="checkbox" name="prom" id="prom" value="prom">
                    <label for="prom">Promotion</label>
                </fieldset>
            </form>
        </section>
        <label for="tri">Trier par </label>
        <select id="tri" value ="triOption">
            <option value="nom_public" data-name ="ASC">Ordre alphabétique</option>
            <option value="note_moy" data-name ="DESC">Meilleurs avis</option>
            <option value="triPrix" data-name ="ASC">Prix croissants</option>
            <option value="triPrixCroi" data-name ="DESC">Prix décroissants</option>
            <!-- <option value="triReduc" data-name ="ASC">Réduction</option> -->
        </select>
        <h1 id="results_for">Résultats pour "<?= $recherche ?>"</h1>

        <section class="results">
            <!-- Grille des résultats -->
            <ul id="results"></ul>
        </section>
    </main>
    <?php include HOME_SITE . "footer.php" ?>
    <style>
        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .filters fieldset {
            border: none;
            padding: 0;
            margin-bottom: 1.5rem;
        }

        .filters legend {
            font-weight: 600;
            margin-bottom: .75rem;
        }

        .filters label {
            display: block;
            margin-bottom: .5rem;
            cursor: pointer;
        }
        .results ul {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.5rem;
            list-style: none;
            padding: 0;
        }

        .results li a {
            display: block;
            background: white;
            padding: 1rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            color: inherit;
            text-decoration: none;
            transition: transform .2s, box-shadow .2s;
        }

        .results li a:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0,0,0,.12);
        }

        .results img {
            width: 100%;
            aspect-ratio: 1/1;
            object-fit: cover;
            margin-bottom: .75rem;
        }

        .prix {
            color: var(--primary);
            font-weight: 600;
        }

        .ancien_prix {
            text-decoration: line-through;
            color: #999;
            font-size: .9rem;
        }

    </style>
</body>

</html>
