<?php
    define('HOME_GIT', "../../" );
    define('HOME_SITE', "../" );

    if (!isset($_SESSION)) {
        session_start();

        if(isset($_SESSION['raison_sociale'])){
            header('location: /vendeur/stock/');
        }
    }

    require_once (HOME_GIT . '.config.php');
    require_once (HOME_GIT . 'fonction_produit.php');
    require_once (HOME_GIT . 'fonction_categorie.php');
    require_once (HOME_GIT . 'fonction_global.php');
    require_once (HOME_GIT . 'fonction_vendeur.php');
    
    $tab_vendeurs = get_coor_id_vendeur();
    $tab_adresse = get_adresse();
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="Page accueil" content="width=device-width, initial-scale=1.0">
        <?php include HOME_SITE . "link_head.php"; ?>
        <title>Alizon - Carte des vendeurs</title>

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
    </head>
    <body>
        <?php 
            include HOME_SITE . "header.php";
            include HOME_SITE . "toolbar_categories.php";
        ?>
        <main id="main_map">
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
        </main>
        <?php include HOME_SITE . "footer.php" ?>
    </body>
    <script>

        afficher_listes_vendeur()

        const tab_vendeurs = <?= json_encode($tab_vendeurs)?>;
        const tab_adresse = <?= json_encode($tab_adresse)?>;

        // Filtres 
        const liste_vendeur = document.getElementById("liste_vendeur")
        const init = document.getElementById("init")
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
</html>