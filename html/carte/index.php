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
                <div id="tri_departement">
                    <h2>Departement</h2>
                    <div>
                        <label for="finistere">Finistere - 29</label>
                        <input type="checkbox" id="finistere">
                    </div>
                    <div>
                        <label for="cotedarmor">Cote d'armor - 22</label>
                        <input type="checkbox" id="cotedarmor">
                    </div>
                    <div>
                        <label for="morbihan">Morbihan - 56</label>
                        <input type="checkbox" id="morbihan">
                    </div>
                    <div>
                        <label for="ileetvillaine">Ile et villaine - 35</label>
                        <input type="checkbox" id="ileetvillaine">
                    </div>
                </div>
            </section>
            <div id="map"></div>
        </main>
        <?php include HOME_SITE . "footer.php" ?>
    </body>
    <script>
        let map = L.map('map').setView([48.003,-2.192],8)

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map)

    </script>
</html>