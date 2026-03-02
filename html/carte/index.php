<?php
define('HOME_GIT', "../../" );
define('HOME_SITE', "../" );

if (!isset($_SESSION)) {
    session_start();

    if(isset($_SESSION['raison_sociale'])){
        header('location: /vendeur/stock/');
    }
}
?>

<!DOCTYPE html>
<html>
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
        <main>
            <div id="map"></div>
        </main>
        <?php include HOME_SITE . "footer.php" ?>
    </body>
    <script>
        let map = L.map('map').setView([48.003,-2.192],7)

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map)

    </script>
</html>