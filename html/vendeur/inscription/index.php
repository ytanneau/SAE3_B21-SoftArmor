<?php
    define('HOME_GIT', '../../../');
    define('HOME_SITE', '../../');

    if (!isset($_SESSION)) {
        session_start();
    }

    // Si connecté en vendeur, rediriger vers le stock, si connecté en client, rediriger vers l'accueil
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        header(isset($_SESSION['raison_sociale']) ? 'location: ../accueil' : 'location: ' . HOME_SITE);
    }
    
    require_once HOME_GIT . 'fonction_vendeur.php';

    if ($_POST != null) {
        $erreurs = [];
        $fichier = HOME_GIT . 'fonction_compte.php';
        
        if (file_exists($fichier)) {
            require_once $fichier;
            if($_POST['stage'] == 0){
                $adresseSubmit = $_POST['adresse'] . $_POST['compAdresse'] . ", " . $_POST['ville'] . ", " . $_POST['codePostal'];
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
                    $longitude = $data["lat"];
                    $latitude = $data["lon"];
                }
                
                $erreurs = create_profile_vendeur($_POST['raisonSocial'], $_POST['numSiret'], $_POST['numCobrec'], $_POST['email'], $_POST['ville'], $_POST['adresse'], $_POST['compAdresse'], $_POST['codePostal'], $_POST['mdp'], $_POST['mdpc'], HOME_GIT, $longitude, $latitude);
                $id_compte = $erreurs['id_compte'];
                array_pop($erreurs);
                
            }
            else if($_POST['stage'] == 1){
                $_POST['stage'] = 2;
                $lon = $_POST['longitude'];
                $lat = $_POST['latitude'];
                $id_adresse = get_adresse_vendeur_with_vendeur_id($id_compte);
                set_lon_lat($id_adresse, $lon, $lat);
                if (empty($erreurs)) {
                    // L'inscription est réussie, donc connexion directe
                    connect_compte($_POST['email'], $_POST['mdp'], "vendeur", "");
                    $_SESSION['logged_in'] = true;
                }
            }
            
        } else {
            $erreurs['fatal'] = true;
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include HOME_SITE . 'link_head.php'; ?>
    <title>Alizon - Inscription</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>
    
</head>
<body id="inscription_vendeur">
<?php if(isset($erreurs) && $erreurs == [] && $_POST['stage'] == 1){
?>
    <main class="mainCarteInscription">
        <h1>Confirmer les coordonnées</h1>
        <div id="map"></div>
        <form action="" id="formCoordonnee" method="post">
            <div>
                <label for="adresseSaisi">Adresse saisi : </label>
                <input type="text" id="adresseSaisi" value="<?= htmlspecialchars($adresseSubmit)?>">
            </div>
            <div>
                <label for="longitude">Longitude</label>
                <input type="text" id="longitude" value="<?= htmlspecialchars($longitude) ?>" name="longitude">
                <label for="latitude">Latitude</label>
                <input type="text" id="latitude" value="<?= htmlspecialchars($latitude) ?>" name="latitude">
            </div>
            <input type="text" name="stage" hidden value="1">

            <input type="submit" class="bouton">
        </form>
    <script src="script_map.js"></script>
<?php } elseif (isset($erreurs) && $erreurs == [] && $_POST['stage'] == 2) { ?>
<main>
        <h1>Votre compte a été créé</h1>
        <p>Voulez-vous activer la double authentification ? <a href="../../authentikator/activer.php">Cliquez ici</a></p>
        <p><a href="../stock">Aller à la page d'accueil</a></p>

<?php } elseif (isset($erreurs['fatal'])){ ?>
        <h1 class="fatale">Désolé, nous rencontrons des problèmes serveur</h1>

<?php } else { ?>
<main>
    <img src=""  alt="">
    <a href="../"><img src="<?=HOME_SITE?>image/Alizon_vendeur_noir.png" alt="logo alizon" title="logo alizon"></a>

    <h2>S'inscrire</h2>

    <form action="" method="post">
        <label for="raisonSocial">Raison sociale</label>
        <input type="text" 
            name="raisonSocial"
            id="raisonSocial"
            minlength="3"
            maxlength="60"
            placeholder="ARMOR LUX SAS"
            value="<?php if (isset($_POST['raisonSocial'])) echo htmlentities($_POST['raisonSocial'])?>"
            required
            class="champ">
        <p class="contrainte">Nom puis statut juridique</p>
    
        <?php if (isset($erreurs['raison_sociale'])){ ?>
            <p class="error">
                <?="Erreur : ".$erreurs['raison_sociale']?>
            </p>
        <?php } ?>
            
        <label for="numSiret">Numéro de SIRET</label>
        <input type="text" 
            name="numSiret"
            id="numSiret"
            minlength="14"
            placeholder="362 521 879 00034"
            value="<?php if (isset($_POST['numSiret'])) echo htmlentities($_POST['numSiret'])?>"
            required
            class="champ">
        <p class="contrainte">Numéro à 14 chiffres</p>
        
        <?php if (isset($erreurs['numero_siret'])){ ?>
            <p class="error">
                <?="Erreur : ".$erreurs['numero_siret']?>
            </p>
        <?php } ?>

            
        <label for="numCobrec">Clé de la COBREC</label>
        <input type="text" 
            name="numCobrec"
            id="numCobrec"
            minlenght="15"
            placeholder="12345-12345-12345"
            value="<?php if (isset($_POST['numCobrec'])) echo htmlentities($_POST['numCobrec'])?>"
            required
            class="champ">
        <p class="contrainte">Numéro a 15 chiffres donné par la COBREC</p>

        <?php if (isset($erreurs['numero_cobrec'])){ ?>
            <p class="error">
                <?="Erreur : ".$erreurs['numero_cobrec']?>
            </p>
        <?php } ?>

            
        <label for="email">Email</label>
        <input type="email"
            name="email"
            id="email"
            placeholder="exemple@email.com"
            value="<?php if (isset($_POST['email'])) echo htmlentities($_POST['email'])?>"
            required
            class="champ">
        <p class="contrainte"></p>
    
        <?php if (isset($erreurs['email'])){ ?>
            <p class="error">
                <?="Erreur : ".$erreurs['email']?>
            </p>
        <?php } ?>



        <label for="ville">Ville du siège</label>
        <input type="text"
            name="ville"
            id="ville"
            value="<?php if (isset($_POST['ville'])) echo htmlentities($_POST['ville'])?>"
            required
            class="champ">
        <p class="contrainte">ex : Paris</p>
    
        <?php if (isset($erreurs['ville'])){ ?>
            <p class="error">
                <?="Erreur : ".$erreurs['ville']?>
            </p>
        <?php } ?>


        <label for="adresse">Adresse du siège</label>
        <input type="text"
            name="adresse"
            id="adresse"
            value="<?php if (isset($_POST['adresse'])) echo htmlentities($_POST['adresse'])?>"
            required
            class="champ">
        <p class="contrainte">Numéro, nom de la voie</p>

        <?php if (isset($erreurs['adresse'])){ ?>
            <p class="error">
                <?="Erreur : ".$erreurs['adresse']?>
            </p>
        <?php } ?>

            
        <label for="compAdresse">Complément d'adresse</label>
        <textarea type="text"
            name="compAdresse"
            id="compAdresse"
            class="champ text"><?php 
            if (isset($_POST['compAdresse'])) echo trim(htmlentities($_POST['compAdresse']))
        ?></textarea>

        <p class="contrainte">Informations complémentaires</p>

            
        <label for="codePostal">Code postal</label>
        <input type="number" 
            name="codePostal"
            id="codePostal"
            size="5"
            value="<?php if (isset($_POST['codePostal'])) echo htmlentities($_POST['codePostal'])?>"
            required
            class="champ">
        <p class="contrainte">Nombre à 5 chiffres</p>
    
        <?php if (isset($erreurs['code_postal'])){ ?>
            <p class="error">
                <?="Erreur : ".$erreurs['code_postal']?>
            </p>
        <?php } ?>

            
        <label for="mdp">Mot de passe</label>
        <input type="password" 
            name="mdp"
            id="mdp"
            minlength="12"
            maxlength="100"
            required
            class="champ">
        <p class="contrainte">Minimum 12 caractères</p>

        <?php if (isset($erreurs['mdp'])){ ?>
            <p class="error">
                <?="Erreur : ".$erreurs['mdp']?>
            </p>
            
        <?php } ?>

            
        <label for="mdpc">Mot de passe de confirmation</label>
        <input type="password" 
            name="mdpc"
            id="mdpc"
            minlength="12"
            maxlength="100"
            required
            class="champ">
        <p class="contrainte"></p>

        <?php if (isset($erreurs['mdpc'])){ ?>
            <p class="error">
                <?="Erreur : ".$erreurs['mdpc']?>
            </p>
        <?php } ?>
            <input type="text" name="stage" hidden value="0">
            <input type="submit" value="S'inscrire" class="bouton">
        </form>

        <p style="text-align:center;">Déjà inscrit ? <a href="../">Se connecter</a>
        <br>
        <a href="../">Retourner au côté client</a></p>
<?php } ?>
    </main>
</body>
</html>