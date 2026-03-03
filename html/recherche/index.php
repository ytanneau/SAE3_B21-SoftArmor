<?php

// Inclusion du fichier de configuration
define('HOME_GIT', '../../');
define('HOME_SITE', '../');

if (!isset($_SESSION)) {
    session_start();

    if (isset($_SESSION['raison_sociale'])){
        header('location: ' . HOME_SITE . '/vendeur/stock/');
        die();
    }
}

$recherche = htmlentities(trim($_GET['recherche'] ?? ''));
$categorie = trim($_GET['categorie'] ?? '');

if (empty($recherche) && empty($categorie)) {
    header('location: ' . HOME_SITE);
    die();
}

require_once (HOME_GIT . '.config.php');
require_once (HOME_GIT . 'fonction_avis.php');
require_once (HOME_GIT . 'fonction_produit.php');
require_once (HOME_GIT . 'fonction_global.php');
require_once (HOME_GIT . 'fonction_panier.php');
require_once (HOME_GIT . 'fonction_categorie.php');
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
    <?php 
        include HOME_SITE . "header.php";
        include HOME_SITE . "toolbar_categories.php";
    ?>

    <main class ="pageRecherche">
        <div class="TriEtFiltre">
            <form class="formTri">
                <fieldset id = "prixF">
                    <legend>Filtrer par prix</legend>
                    <div class="sousFiltre">
                        <input type="radio" name="prix" id="zeroTo20" value="zeroTo20">
                        <label for="zeroTo20">0 € à 20 €</label>
                    </div>
                    <div class="sousFiltre">
                        <input type="radio" name="prix" id="twentyTo50" value="twentyTo50">
                        <label for="twentyTo50">20 € à 50 €</label>
                    </div>
                    <div class="sousFiltre">
                        <input type="radio" name="prix" id="fiftyTo100" value="fiftyTo100">
                        <label for="fiftyTo100">50 € à 100 €</label>
                    </div>
                    <div class="sousFiltre">
                        <input type="radio" name="prix" id="hundredTo300" value="hundredTo300">
                        <label for="hundredTo300">100 € à 300 €</label>
                    </div>
                    <div class="sousFiltre">
                        <input type="radio" name="prix" id="over300" value="over300">
                        <label for="over300">Plus de 300 €</label>
                    </div>
                    <div class="sousFiltre">
                        <input type="checkbox" name="prom" id="prom" value="prom">
                        <label for="prom">Promotion</label>
                    </div>
                    <div class="sousFiltre">
                        <input type="checkbox" name="reduc" id="reduc" value="reduc">
                        <label for="reduc">Réduction</label>
                    </div>
                    <div class="sousFiltre">
                        <button type="button" id="resetFilters">
                            Supprimer les filtres
                        </button>
                    </div>
                </fieldset>
            </form>
        </div>
        <div class="resultat">
            <div>
                <div class="">
                    <span id="for_category"></span>
                    <h1 id="results_for"></h1>
                </div>
                <div class="labelEtBandeau">
                    <div class="labelBendeau">
                        <label for="tri">Trier par </label>
                    </div>
                    <div class="deroulant">
                        <select id="tri" value ="triOption">
                            <option value="nom_public" data-name ="ASC">Ordre alphabétique</option>
                            <option value="note_moy" data-name ="DESC">Meilleurs avis</option>
                            <option value="triPrix" data-name ="ASC">Prix croissants</option>
                            <option value="triPrixCroi" data-name ="DESC">Prix décroissants</option>
                            <option value="triReduc" data-name ="DESC">Réduction</option>
                        </select>
                    </div>
                </div>
            </div>
            <section class="results">
                <!-- Grille des résultats -->
                <ul id="results"></ul>
            </section>

            <!-- Zone de la carte -->
            <button id="openPanel">Ouvrir la carte</button>
            <section id="sidePanel">
                <h2>Carte</h2>
            </section>
            <script>
                const btn_open = document.getElementById("openPanel")
                const panel = document.getElementById("sidePanel")

                btn_open.addEventListener("click", () => {
                    panel.classList.add("active");
                    //overlay.classList.add("active");
                });
            </script>
        </div>
    </main>

    <script type="text/javascript">
        const searchState = {
            search: "<?=$recherche?>",
            filters: {
                category: "<?=$categorie?>",
                price: {min: null, max: null},
                sales: false,
                reduc: false
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
        const forCategory = document.querySelector("#for_category");
        
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

        let reducCheck = document.getElementById("reduc");

        reducCheck.addEventListener('change', (e) => {
            let isChecked = e.target.checked;
            if (isChecked) {
                console.log(e.target.value);
                searchState.filters.reduc = true;
                console.log(searchState.filters.reduc);
            } else {
                console.log("Unchecked");
                searchState.filters.reduc = false;
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
            if (searchState.search === "" && searchState.filters.category === "") {
                window.location.replace("..");
            }

            fetchProduitsJSON();
        });

        if (form) {
            form.addEventListener("submit", (e) => {
                if (isSearchPage) {
                    e.preventDefault();
                    
                    // Rediriger si la recherche est vide
                    if (input.value.trim() === "" && searchState.filters.category === "") {
                        window.location.replace("..");
                    }
                    
                    searchState.search = input.value.trim();
                    // searchState.page = 1;
                    fetchProduitsJSON();
                }
            });
        }

        const resetBtn = document.getElementById("resetFilters");

        resetBtn.addEventListener("click", () => {
            // 1. Reset de l’état des filtres
            searchState.filters.price.min = null;
            searchState.filters.price.max = null;
            searchState.filters.sales = false;
            searchState.filters.reduc = false;

            document.querySelectorAll('input[name="prix"]').forEach(radio => radio.checked = false);

            document.getElementById("prom").checked = false;
            document.getElementById("reduc").checked = false;

            const selectTri = document.getElementById("tri");
            selectTri.selectedIndex = 0;

            searchState.sort.field = "nom_public";
            searchState.sort.order = "ASC";

            fetchProduitsJSON();
        });


        function afficherProduits(data) {
            const resultGrid = document.querySelector("#results");
            console.log(resultGrid);
            // Vider les produits déjà présents dans la grille
            while (resultGrid.firstChild) {
                resultGrid.removeChild(resultGrid.firstChild);
            }

            if (searchState.filters.category !== "") {
                forCategory.textContent = `Catégorie "${searchState.filters.category}"`;
            } else {
                forCategory.textContent = "";
            }


            if (searchState.search !== "") {
                resultsFor.textContent = `${data.total} résultat${data.total > 1 ? 's' : ''} pour "${searchState.search}"`;
            } else {
                resultsFor.textContent = "";
            }

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
    </script>

    <?php include HOME_SITE . "footer.php" ?>
</body>

</html>
