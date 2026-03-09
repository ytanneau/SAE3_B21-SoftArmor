<?php 
    // appel du fichier de configuration bdd
    define('HOME_GIT', '../../../../');
    define('HOME_SITE', '../../../');

    if (!isset($_SESSION)) {
        session_start();
    }

    // Si je suis connecté mais pas en tant que vendeur, retour à l'accueil client
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && !isset($_SESSION['raison_sociale'])) {
        header('location: ' . HOME_SITE);
        exit;

    // Sinon si je ne suis pas connecté, retour à la page connexion vendeur
    } else if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] === false) {
        header('location: ../');
        exit;
    }

    // appel des fichiers de configuration et fonctions
    require_once HOME_GIT . ".config.php";
    include HOME_GIT . "fonction_vendeur.php";
    require_once HOME_GIT . "fonction_2FA.php";

    $id_compte = $_SESSION['id_compte'];

    // recuperation des informations vendeur
    $tabVendeur = get_informations_vendeur($id_compte);

    // definition des variables suivant les valeurs du tableau
    $raisonSociale = $tabVendeur['raison_sociale'];
    $description = $tabVendeur['description'];
    $id_adresse = $tabVendeur['id_adresse'];

    // recuperation des informations d'adresse du vendeur
    $tabAdresseVendeur = get_adresse_vendeur($id_adresse);
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // récupération des données du formulaire de saisie
        $modifRaisonSociale = $_POST['raison_sociale'];
        $modifVille = $_POST['ville'];
        $modifAdresse = $_POST['adresse'];
        $modifCodePostal = $_POST['code_postal'];
        $modifCompelementAdr = $_POST['complementAdr'];
        $modifDescription = $_POST['description'];
        // redifinition des coordonnées suivant la nouvelle adresse
        if($modifVille != $tabAdresseVendeur['ville'] || 
        $modifAdresse != $tabAdresseVendeur['adresse'] ||
        $modifCodePostal != $tabAdresseVendeur['code_postal']){
            $adresseSubmit = $modifAdresse . ", " . $modifVille. ", " . $modifCodePostal;
            $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($adresseSubmit);
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT => "marketplace-test",

                CURLOPT_PROXY => "10.254.0.254:3128",
                CURLOPT_PROXYTYPE => CURLPROXY_HTTP,

                CURLOPT_PROXYUSERPWD => "sae301_b21:a9ntNhsglad)",

                CURLOPT_HTTPPROXYTUNNEL => true,

                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);
            
            $response = curl_exec($ch);

            if(curl_errno($ch)){
                echo "Erreur cURL : " . curl_error($ch);
            }
            $data = json_decode($response, true);

            if(!empty($data)){
                $data = $data[0];
                $lon = $data["lat"];
                $lat = $data["lon"];
            } else if(empty($data)){
                $lon = $tabAdresseVendeur['lon'];
                $lat = $tabAdresseVendeur['lat'];
            }
        }
        if(isset($_POST['lon']) && isset($_POST['lat'])){
            if($_POST['lon'] == null){$lon = $tabAdresseVendeur['lon'];} 
            else {$lon = $_POST['lon'];}
            if($_POST['lat'] == null){$lat = $tabAdresseVendeur['lat'];}
            else {$lat = $_POST['lat'];}
        }
        
        $_SESSION['raison_sociale'] = $modifRaisonSociale;

        // Mise à jour des informations dans la base de donnée
        update_informations_vendeur($modifRaisonSociale, $modifDescription, $id_compte);

        // mise à jour de l'adresse du vendeur
        update_adresse_vendeur($id_compte, $modifVille, $modifAdresse, $modifCodePostal, $modifCompelementAdr, $lon, $lat);

        // redirection vers la page precedente apres la validation du formulaire
        header('Location: ../../accueil/');
        exit();
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include HOME_SITE . 'link_head.php';?>
        <meta charset="UTF-8">
        <title>Alizon - Mes informations</title>

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
    </head>
    <body>
        <!-- inclusion du header -->
        <?php include "../../header.php"?>
        <main class="mainInfoVendeur">
            <a href="../../accueil"><img src="<?=HOME_SITE?>image/retour.svg" alt="bouton retour en arrière"></a>
            <div>
                <div class="entete">
                    <!-- Bouton de retour sur la page d'accueil -->
                    <h1>Mes informations</h1>
                </div>
                <em>Pour des raisons de securité, le numéro de SIRET ne peut être modifié</em>

                <!-- formulaire de saisie des modifications des informations d'un vendeur -->
                <form action="" name="formulaireModif" method="post" enctype="multipart/form-data">
                    <div>
                        <p>
                            <label for="raison_sociale">Raison sociale</label>
                            <input type="text" name="raison_sociale" id="raison_sociale" value="<?= $raisonSociale ?>">
                            <label for="ville">Ville</label>
                            <input type="text" name="ville" id="ville" value="<?= $tabAdresseVendeur['ville'] ?>">
                            <label for="adresse">Adresse</label>
                            <input type="text" name="adresse" id="adresse" value="<?= $tabAdresseVendeur['adresse'] ?>">
                            <label for="code_postal">Code postal</label>
                            <input type="text" name="code_postal" id="code_postal" value="<?= $tabAdresseVendeur['code_postal'] ?>">
                            <label for="complementAdr">Complement d'adresse</label>
                            <textarea type="text" name="complementAdr" id="complementAdr"><?= $tabAdresseVendeur['complement_adresse'] ?></textarea>
                            <label for="idDescSimple">Description</label>
                            <textarea type="textarea" name="description" id="idDescSimple"><?= $description ?></textarea>
                        </p>
                        <h2 id="warningTitle">Corriger mes coordonnées</h2>
                        <?php if($tabAdresseVendeur['lon'] != null || $tabAdresseVendeur['lat'] != null ){?>
                        <div id="map"></div>
                        <div class="inputs_lon_lat">
                            <div>
                                <label for="longitude">Longitude</label>
                                <input type="text" name="lon" id="longitude" value="<?= $tabAdresseVendeur['lon']?>">
                            </div>
                            <div>
                                <label for="latitude">Latitude</label>
                                <input type="text" name="lat" id="latitude" value="<?= $tabAdresseVendeur['lat']?>">
                            </div>
                        </div>
                        <?php }?>
                    </div>
                    <input type="submit" value="Valider la modification" id="idValiderModifVendeur">
                </form>
                <a href="desactivation/desactivation.php" id="idDesactivationCompte">Désactiver le compte</a>

                <!-- boutons en rapport avec la double authentification -->
                <?php if (!a_2FA($_SESSION['id_compte'])) { ?>
                    <a href="<?= HOME_SITE . "authentikator/activer.php" ?>" id="idActivation2FA">Activer la 2FA</a>
                <?php } else { ?>
                    <a href="<?= HOME_SITE . "authentikator/desactiver.php" ?>" id="idDesactivation2FA">Désactiver la 2FA</a>
                <?php } ?>
            </div>
        </main>
        <?php include HOME_SITE . "footer.php"?>
    </body>
    <script>
        const adresseVendeur = <?= json_encode($tabAdresseVendeur)?>;
        let lon = adresseVendeur['lon']
        let lat = adresseVendeur['lat']
        let map = L.map('map')
        if(lon != null || lat != null){
            let marker = L.marker([parseFloat(lon),parseFloat(lat)]).addTo(map)
            map.setView([lon,lat],18)
        } else {
            const warningTitle = document.getElementById("warningTitle");
            warningTitle.innerHTML = "Erreur de coordonnées"
        }

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map)
        
        
    </script>
</html>