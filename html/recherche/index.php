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
<body data-page="search">
    <?php include HOME_SITE . "header.php"; ?>

    <section class="filters">
        <form>
            <fieldset>
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
            </fieldset>
        </form>
</section>
        <select id="tri" value ="triOption">
            <option></option>
            <option value="triAvis">Par notes</option>
            <option value="triPrixDecr">Par prix decroissant</option>
            <option value="triPrixCroi">Par prix croissant</option>
            <option value="triPrixCroi">Par prix réduction</option>
        </select>

    <h1 id="results_for">Résultats pour "<?= $recherche ?>"</h1>

    <section class="results">
        <!-- Grille des résultats -->
        <div id="results"></div>
    </section>


    <script type="text/javascript">
        const searchState = {
            search: "<?=$recherche?>",
            filters: {
                category: [],
                price: {min: null, max: null},
                sales: false
            },
            sort: {
                field: "nom_public",
                order: "asc"
            }
        };

        // Est-on déjà sur la page de recherche ?
        const isSearchPage = document.body.dataset.page === "search";
        const form = document.querySelector("#form_recherche");
        const input = document.querySelector("#recherche");
        const resultsFor = document.querySelector("#results_for");

        // Une fois la page chargée, récupérer une première fois les produits avec la recherche initiale
        document.addEventListener("DOMContentLoaded", () => {
            fetchProduitsJSON();
        });
        //listener pour les tris et renvoi 
        var s = document.getElementById("tri");
        var selNum = s.options[s.selectedIndex].value;
        var selName = s.options[s.selectedIndex].dataset.name;
        s.addEventListener("change", (e) => {
            if (isSearchPage) {
                e.preventDefault();

                searchState.sort.field = selNum;
                searchState.sort.order = selName;
                console.log(searchState.sort.order);
                console.log(searchState.sort.field);
            }
        });

        if (form) {
            form.addEventListener("submit", (e) => {
                if (isSearchPage) {
                    e.preventDefault();

                    searchState.search = input.value;
                    // searchState.page = 1;

                    fetchProduitsJSON();
                    resultsFor.textContent = `Résultats pour "${input.value}"`;
                }
            });
        }

        // Récupérer tous les produits dans un objet JSON
        async function fetchProduitsJSON() {
            fetch('/recherche/produits.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(searchState)
            })
            .then(res => res.json())
            .then(data => {
                afficherProduits(data);
            });
        }

        function afficherProduits(data) {
            const resultGrid = document.querySelector("#results");
            // console.log(data.produits);

            // Exemple pour créer une balise
            // let monAdr = document.createElement("a");
            // let attribut = document.createAttribute("href");
            // attribut.value = "mailto:jbond@scot-yard.uk";
            // monAdr.setAttributeNode(attribut);
            // monContact.appendChild(monAdr);

            // Vider les produits déjà présents dans la grille
            while (resultGrid.firstChild) {
                resultGrid.removeChild(resultGrid.firstChild);
            }

            data.produits.forEach(produit => {
                let paragraphe = document.createElement("p");
                let texteNom = document.createTextNode(produit.nom_public);
                paragraphe.appendChild(texteNom);
        
                resultGrid.appendChild(paragraphe);
            });
        }
    </script>
</body>
<!--
<a href="">
    <img src="" title="" alt="">
    <h3></h3>
    <div>
        <img src="" alt="" title="" class="etoile">
        <img src="" alt="" title="" class="etoile">
        <img src="" alt="" title="" class="etoile">
        <img src="" alt="" title="" class="etoile">
        <img src="" alt="" title="" class="etoile">
    </div>
    <p class="ancien_prix"></p> Si réduction
    <p class="prix"></p>
</a>
-->

</html>
