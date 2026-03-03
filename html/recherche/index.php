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
require_once (HOME_GIT . "fonction_vendeur.php");

    $tab_vendeurs = get_coor_id_vendeur();
    $tab_adresse = get_adresse();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include HOME_SITE . "link_head.php" ?>
    <title>Alizon - Recherche</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>
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
        </div>
        <!-- Zone de la carte -->
        <button id="btnOuvrirCarte">Ouvrir la carte</button>

        <div id="ombre"></div>

        <section id="panneauCarte">
            <button id="btnFermerCarte">✕</button>
            <h2>Carte</h2>
            <section>
                <button id="btn_reset_filter">Supprimer les filtres</button>
                <div id="tri_departement">
                    <h2>Departement</h2>
                    <div>
                        <label for="cotedarmor">Cote d'armor - 22</label>
                        <input type="checkbox" id="cotedarmor">
                    </div>
                    <div>
                        <label for="finistere">Finistere - 29</label>
                        <input type="checkbox" id="finistere">
                    </div>
                    <div>
                        <label for="illeetvilaine">Ille et vilaine - 35</label>
                        <input type="checkbox" id="illeetvilaine">
                    </div>
                    <div>
                        <label for="morbihan">Morbihan - 56</label>
                        <input type="checkbox" id="morbihan">
                    </div>
                </div>
                <div id="liste_vendeur">
                    <h2>Les vendeurs</h2>
                    <div id="init"></div>
                </div>
            </section>
            <div id="map"></div>
        </section>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const btnOuvrir = document.getElementById("btnOuvrirCarte");
                const btnFermer = document.getElementById("btnFermerCarte");
                const carte = document.getElementById("panneauCarte");
                const ombre = document.getElementById("ombre");

                btnOuvrir.addEventListener("click", () => {
                    carte.classList.add("active");
                    ombre.classList.add("active");
                    document.body.style.overflow = "hidden"; // bloque scroll
                });

                function closePanel() {
                    carte.classList.remove("active");
                    ombre.classList.remove("active");
                    document.body.style.overflow = "auto";
                }

                btnFermer.addEventListener("click", closePanel);
                ombre.addEventListener("click", closePanel);

            });

            // AFFICHAGE DES FILTRES
            const tab_vendeurs = <?= json_encode($tab_vendeurs)?>;
            const tab_adresse = <?= json_encode($tab_adresse)?>;
            const liste_vendeur = document.getElementById("liste_vendeur")
            const init = document.getElementById("init")

            function afficher_listes_vendeur(){
                tab_vendeurs.forEach(vendeur => {
                    let div = document.createElement("div")
                    let input = document.createElement("input")
                    input.type = "checkbox"
                    let label = document.createElement("label")
                    for(let cle in vendeur){
                        if(cle == 'raison_sociale'){
                            input.id = vendeur[cle]
                            label.innerHTML = vendeur[cle]
                            label.htmlFor = vendeur[cle]
                        }
                    }
                    div.appendChild(label)
                    div.appendChild(input)
                    liste_vendeur.insertBefore(div, init)
                });
            }

            afficher_listes_vendeur()

            // GESTIONS DES FILTRES
            const btn_reset_filter = document.getElementById("btn_reset_filter")
            // const cotedarmor = document.getElementById("cotedarmor")
            // const finistere = document.getElementById("finistere")
            // const illeetvilaine = document.getElementById("illeetvilaine")
            // const morbihan = document.getElementById("morbihan")
            const input_checkbox = document.querySelectorAll("input[type=checkbox]")

            console.log(input_checkbox)

            btn_reset_filter.addEventListener('click', (e) => {
                e.preventDefault()
                input_checkbox.forEach(input => {
                    input.checked = false
                })
            })  

            // Initialisation de la carte
            let map = L.map('map').setView([48.113,-2.642],8)

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map)

            tab_vendeurs.forEach(vendeur => {
                let tab_coor = []
                let popup = L.popup()
                let adresse = ""
                let raison_sociale = ""
                for(let info in vendeur){
                    if(info == 'id_adresse'){
                        tab_adresse.forEach(objet_adresse => {
                            for(let cle in objet_adresse){
                                if(cle == 'id_adresse'){
                                    if(vendeur[info] == objet_adresse[cle]){
                                        adresse = objet_adresse['adresse'] + 
                                        objet_adresse['complement_adresse'] + ", " + 
                                        objet_adresse['ville'] + ", " +
                                        objet_adresse['code_postal']
                                    }
                                }
                            }
                        });
                    }
                    if((info == 'coor_x' || info == 'coor_y')&& vendeur[info] != null){
                        tab_coor.push(vendeur[info])
                    } else if (info == 'raison_sociale'){
                        raison_sociale = vendeur[info]
                    }
                }
                if(tab_coor.length == 2){
                    let marker = L.marker(tab_coor).addTo(map)
                    marker.bindPopup("<b>" + raison_sociale + "</b><br> Adresse : <br>" + adresse)
                }
            });
        </script>
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
            // console.log(resultGrid);
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
