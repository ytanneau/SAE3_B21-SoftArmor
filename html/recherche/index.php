<?php

// Inclusion du fichier de configuration
define('HOME_GIT', '../../');
define('HOME_SITE', '../');

if (!isset($_SESSION)) {
    session_start();

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

<h1 id="results_for">Résultats pour "<?= $recherche ?>"</h1>

<section class="results">
    <!-- Grille des résultats -->
    <div id="results"></div>
</section>


<script type="text/javascript">
    let params = new URLSearchParams(document.location.search);
    let query = params.get("recherche");

    const searchState = {
        search: query,
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
        // Rediriger si la recherche est vide
        if (searchState.search === "") {
            window.location.replace("..");
        }
        
        fetchProduitsJSON();
    });

    if (form) {
        form.addEventListener("submit", (e) => {
            if (isSearchPage) {
                e.preventDefault();
                searchState.search = input.value.trim();
                // searchState.page = 1;
                
                // Rediriger si la recherche est vide
                if (searchState.search === "") {
                    window.location.replace("..");
                }

                fetchProduitsJSON();
            }
        });
    }

    function afficherProduits(data) {
        const resultGrid = document.querySelector("#results");

        // Vider les produits déjà présents dans la grille
        while (resultGrid.firstChild) {
            resultGrid.removeChild(resultGrid.firstChild);
        }

        resultsFor.textContent = `${data.total} résultat${data.total > 1 ? 's' : ''} pour "${searchState.search}"`;

        console.log(data.produits);

        data.produits.forEach(produit => {
            // Lien vers la page produit
            let lien = document.createElement("a");
            let ref = document.createAttribute("href");
            ref.value = `/produit/?produit=${produit.id_produit}`;
            lien.setAttributeNode(ref);

            // Image du produit

            let photo = image(`../${produit.url_image}`, produit.alt, produit.titre);

            // Titre du produit
            let titreNomPublic = document.createElement("h3");
            let nomPublic = document.createTextNode(produit.nom_public);
            titreNomPublic.appendChild(nomPublic);

            // Étoiles
            let divEtoiles = listeEtoiles(produit.note_moy);

            if (divEtoiles == null) {
                divEtoiles = document.createElement("div");

                let pNonNote = document.createElement("p");
                let texteNonNote = document.createTextNode("Produit Non Noté");

                pNonNote.appendChild(texteNonNote);
                divEtoiles.appendChild(pNonNote);
            }
    
            lien.appendChild(photo);
            lien.appendChild(titreNomPublic);
            lien.appendChild(divEtoiles);

            resultGrid.appendChild(lien);
        });
    }

    function listeEtoiles(moyenne) {
        let divEtoiles = document.createElement("div");

        if (moyenne == null || moyenne < 0 || moyenne > 5) {
            return null;
        }

        for (let i = 0; i < Math.floor(moyenne); i++) {
            divEtoiles.appendChild(image('/image/etoile_pleine.svg', 'étoile pleine', 'étoile pleine'));
        }

        if (Math.floor(moyenne * 2) % 2 != 0) {
            divEtoiles.appendChild(image('/image/etoile_demi.svg', 'étoile à moitié pleine', 'étoile à moitié pleine'));
        }

        for (let i = 5; i > Math.round(moyenne); i--) {
            divEtoiles.appendChild(image('/image/etoile_vide.svg', 'étoile vide', 'étoile vide'));
        }
    }

    function image(src, alt, title) {
        let img = document.createElement("img");

        let srcAtt = document.createAttribute("src");
        let altAtt = document.createAttribute("alt");
        let titleAtt = document.createAttribute("title");

        srcAtt.value = src;
        altAtt.value = alt;
        titleAtt.value = title;

        img.setAttributeNode(srcAtt);
        img.setAttributeNode(altAtt);
        img.setAttributeNode(titleAtt);

        return img;
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
