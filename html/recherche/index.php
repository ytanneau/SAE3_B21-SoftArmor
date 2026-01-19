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
        <div class="range_container">
            <div class="sliders_control">
                <input id="fromSlider" type="range" value="10" min="0" max="100"/>
                <input id="toSlider" type="range" value="40" min="0" max="100"/>
            </div>
            <div class="form_control">
                <div class="form_control_container">
                    <div class="form_control_container__time">Min</div>
                    <input class="form_control_container__time__input" type="number" id="fromInput" value="10" min="0" max="100"/>
                </div>
                <div class="form_control_container">
                    <div class="form_control_container__time">Max</div>
                    <input class="form_control_container__time__input" type="number" id="toInput" value="40" min="0" max="100"/>
                </div>
            </div>
        </div>
    </main>

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
        
        let promCheck = document.getElementById("prom");

        promCheck.addEventListener('change', (e) => {
            let isChecked = e.target.checked;
            if (isChecked) {
                console.log(e.target.value);
                searchState.filters.sales = true;
                console.log(searchState.filters.sales);
            } else {
                console.log("Unchecked");
                searchState.filters.sales = false;
            }
            fetchProduitsJSON();
        });
        //listener pour les tris et renvoi 
        let s = document.getElementById("tri");

        // Actualiser le tri à chaque nouvelle sélection
        s.addEventListener("change", (e) => {
                e.preventDefault();
                let selNum = s.options[s.selectedIndex].value;
                let selName = s.options[s.selectedIndex].dataset.name;
                searchState.sort.field = selNum;
                searchState.sort.order = selName;
                console.log(searchState.sort.field);
                console.log(searchState.sort.order);
                fetchProduitsJSON();
        });

        let radios = document.querySelectorAll("input[name=\"prix\"]");

        radios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                let valueName = document.querySelector('input[name="prix"]:checked').value;
                console.log(valueName);
                if (valueName === "zeroTo20") {
                    searchState.filters.price.min = 0;
                    searchState.filters.price.max = 20;
                }
                else if (valueName === "twentyTo50") {
                    searchState.filters.price.min = 20;
                    searchState.filters.price.max = 50;
                }
                else if (valueName === "fiftyTo100") {
                    searchState.filters.price.min = 50;
                    searchState.filters.price.max = 100;
                }
                else if (valueName === "hundredTo300") {
                    searchState.filters.price.min = 100;
                    searchState.filters.price.max = 300;
                }
                else if (valueName === "over300") {
                    searchState.filters.price.min = 300;
                    searchState.filters.price.max = null;
                }
                else {
                    searchState.filters.price.min = null;
                    searchState.filters.price.max = null;
                }
                fetchProduitsJSON();
            })    
        });

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
                    
                    // Rediriger si la recherche est vide
                    if (input.value.trim() === "") {
                        window.location.replace("..");
                    }
                    
                    searchState.search = input.value.trim();
                    // searchState.page = 1;
                    fetchProduitsJSON();
                }
            });
        }

        function afficherProduits(data) {
            const resultGrid = document.querySelector("#results");
            console.log(resultGrid);
            // Vider les produits déjà présents dans la grille
            while (resultGrid.firstChild) {
                resultGrid.removeChild(resultGrid.firstChild);
            }

            resultsFor.textContent = `${data.total} résultat${data.total > 1 ? 's' : ''} pour "${searchState.search}"`

            data.produits.forEach(produit => {
                let listItem = document.createElement("li");
                
                // Lien vers la page produit
                let lien = document.createElement("a");
                let ref = document.createAttribute("href");
                ref.value = encodeURI(`/produit/?produit=${produit.id_produit}&recherche=${searchState.search}`);
                lien.setAttributeNode(ref);

                // Image du produit

                let photo = image(`../${produit.url_image}`, produit.alt, produit.titre);

                // Titre du produit
                let titreNomPublic = document.createElement("h3");
                let nomPublic = document.createTextNode(produit.nom_public);
                titreNomPublic.appendChild(nomPublic);

                // Étoiles
                let divEtoiles = listeEtoiles(produit.note_moy);

                // Si le produit n'est pas noté, l'indiquer
                if (divEtoiles == null) {
                    divEtoiles = document.createElement("div");

                    let pNonNote = document.createElement("p");
                    let texteNonNote = document.createTextNode("Produit Non Noté");

                    pNonNote.appendChild(texteNonNote);
                    divEtoiles.appendChild(pNonNote);
                }
                
                // Prix du produit (ancien prix si réduction)
                let pPrix = document.createElement("p");
                let prix = Number.parseFloat(produit.prix * (1 + produit.tva / 100)).toFixed(2);
                let textePrix = document.createTextNode(`${prix} €`);
                pPrix.appendChild(textePrix);

                let pPrixReduit = null;

                // Si le produit est en réduction, l'indiquer
                if (produit.prix_actuel != produit.prix) {
                    pPrixReduit = document.createElement("p");
                    let prixReduit = Number.parseFloat(produit.prix_actuel * (1 + produit.tva / 100)).toFixed(2);
                    let textePrixReduit = document.createTextNode(`${prixReduit} €`);
                    pPrixReduit.appendChild(textePrixReduit);
                    
                    pPrix.classList.add("ancien_prix");
                    pPrixReduit.classList.add("prix");
                } else {
                    console.log("test 2");
                    pPrix.classList.add("prix");
                }
        
                lien.appendChild(photo);
                lien.appendChild(titreNomPublic);
                lien.appendChild(pPrix);

                if (produit.prix_actuel != produit.prix) {
                    lien.appendChild(pPrixReduit);
                }

                lien.appendChild(divEtoiles);

                listItem.appendChild(lien);
                resultGrid.appendChild(listItem);
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

            return divEtoiles;
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
        function controlFromInput(fromSlider, fromInput, toInput, controlSlider) {
                const [from, to] = getParsed(fromInput, toInput);
                fillSlider(fromInput, toInput, '#C6C6C6', '#25daa5', controlSlider);
                if (from > to) {
                    fromSlider.value = to;
                    fromInput.value = to;
                } else {
                    fromSlider.value = from;
                }
            }
                
            function controlToInput(toSlider, fromInput, toInput, controlSlider) {
                const [from, to] = getParsed(fromInput, toInput);
                fillSlider(fromInput, toInput, '#C6C6C6', '#25daa5', controlSlider);
                setToggleAccessible(toInput);
                if (from <= to) {
                    toSlider.value = to;
                    toInput.value = to;
                } else {
                    toInput.value = from;
                }
            }

            function controlFromSlider(fromSlider, toSlider, fromInput) {
            const [from, to] = getParsed(fromSlider, toSlider);
            fillSlider(fromSlider, toSlider, '#C6C6C6', '#25daa5', toSlider);
            if (from > to) {
                fromSlider.value = to;
                fromInput.value = to;
            } else {
                fromInput.value = from;
            }
            }

            function controlToSlider(fromSlider, toSlider, toInput) {
            const [from, to] = getParsed(fromSlider, toSlider);
            fillSlider(fromSlider, toSlider, '#C6C6C6', '#25daa5', toSlider);
            setToggleAccessible(toSlider);
            if (from <= to) {
                toSlider.value = to;
                toInput.value = to;
            } else {
                toInput.value = from;
                toSlider.value = from;
            }
            }

            function getParsed(currentFrom, currentTo) {
            const from = parseInt(currentFrom.value, 10);
            const to = parseInt(currentTo.value, 10);
            return [from, to];
            }

            function fillSlider(from, to, sliderColor, rangeColor, controlSlider) {
                const rangeDistance = to.max-to.min;
                const fromPosition = from.value - to.min;
                const toPosition = to.value - to.min;
                controlSlider.style.background = `linear-gradient(
                to right,
                ${sliderColor} 0%,
                ${sliderColor} ${(fromPosition)/(rangeDistance)*100}%,
                ${rangeColor} ${((fromPosition)/(rangeDistance))*100}%,
                ${rangeColor} ${(toPosition)/(rangeDistance)*100}%, 
                ${sliderColor} ${(toPosition)/(rangeDistance)*100}%, 
                ${sliderColor} 100%)`;
            }

            function setToggleAccessible(currentTarget) {
                const toSlider = document.querySelector('#toSlider');
                if (Number(currentTarget.value) <= 0 ) {
                    toSlider.style.zIndex = 2;
                } else {
                    toSlider.style.zIndex = 0;
                }
            }

            const fromSlider = document.querySelector('#fromSlider');
            const toSlider = document.querySelector('#toSlider');
            const fromInput = document.querySelector('#fromInput');
            const toInput = document.querySelector('#toInput');
            fillSlider(fromSlider, toSlider, '#C6C6C6', '#25daa5', toSlider);
            setToggleAccessible(toSlider);

            fromSlider.oninput = () => controlFromSlider(fromSlider, toSlider, fromInput);
            toSlider.oninput = () => controlToSlider(fromSlider, toSlider, toInput);
            fromInput.oninput = () => controlFromInput(fromSlider, fromInput, toInput, toSlider);
            toInput.oninput = () => controlToInput(toSlider, fromInput, toInput, toSlider);
    </script>

    <?php include HOME_SITE . "footer.php" ?>
            <style>
            .range_container {
                display: flex;
                flex-direction: column;
                width: 10%;
                margin-right: 80%;
                margin-top: 20%;
            }

                .sliders_control {
                    position: relative;
                    min-height: 50px;
                }

                .form_control {
                    position: relative;
                    display: flex;
                    justify-content: space-between;
                    font-size: 16px;
                    font-family: "Inter";
                    color: #635a5a;
                }

                input[type=range]::-webkit-slider-thumb {
                    -webkit-appearance: none;
                    pointer-events: all;
                    width: 16px;
                    height: 16px;
                    background-color: #fff;
                    border-radius: 50%;
                    box-shadow: 0 0 0 1px #C6C6C6;
                    cursor: pointer;
                }

                input[type=range]::-moz-range-thumb {
                    -webkit-appearance: none;
                    pointer-events: all;
                    width: 16px;
                    height: 16px;
                    background-color: #fff;
                    border-radius: 50%;
                    box-shadow: 0 0 0 1px #C6C6C6;
                    cursor: pointer;  
                }

                input[type=range]::-webkit-slider-thumb:hover {
                    background: #f7f7f7;
                }

                input[type=range]::-webkit-slider-thumb:active {
                    box-shadow: inset 0 0 3px #387bbe, 0 0 9px #387bbe;
                    -webkit-box-shadow: inset 0 0 3px #387bbe, 0 0 9px #387bbe;
                }

                input[type="number"] {
                    color: #8a8383;
                    width: 50px;
                    height: 30px;
                    font-size: 20px;
                    border: none;
                }

                input[type=number]::-webkit-inner-spin-button, 
                input[type=number]::-webkit-outer-spin-button {  
                    opacity: 1;
                }

                input[type="range"] {
                    -webkit-appearance: none; 
                    appearance: none;
                    height: 2px;
                    width: 100%;
                    position: absolute;
                    background-color: #C6C6C6;
                    pointer-events: none;
                }

                #fromSlider {
                    height: 0;
                    z-index: 1;
                }
        </style>
</body>

</html>
