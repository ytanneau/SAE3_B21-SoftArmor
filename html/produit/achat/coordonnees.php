<?php
const HOME_GIT = "../../../";

if (!isset($_GET['produit'])) {
    header("location: " . HOME_GIT, );
}

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['logged_in'])) {
    header("location: " . HOME_GIT, );
}

require_once HOME_GIT . ".config.php";

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Alizon - Achat</title>
        <meta charset="UTF-8">
        <meta lang="fr">
<?php
$requete = $pdo->prepare("SELECT nom_public, prix, tva, description, description_detaillee FROM produit WHERE id_produit = :id_produit");
$requete->bindValue(":id_produit", $_GET['produit'], PDO::PARAM_STR);
$requete->execute();

$produit = $requete->fetch(PDO::FETCH_ASSOC);

$requete = $pdo->prepare("SELECT adresse, code_postal, complement_adresse FROM client_adresse WHERE id_compte = :id_client");
$requete->bindValue(":id_client", $_SESSION['id_compte'], PDO::PARAM_STR);
$requete->execute();

$adresse_client = $requete->fetch(PDO::FETCH_ASSOC);

?>
    </head>

    <body>
        <h1>Entrez vos coordonnées</h1>
        <form action="" method="post">

        <label for="adresse">Adresse</label>
        <input type="text" name="adresse" id="adresse" value="<?php if (isset($adresse_client['adresse'])) echo $adresse_client['adresse']?>" required>
        <p class="contrainte">ex: 12 rue de la Gare, Paris</p>
        <?php
        if (isset($res['adresse'])){
        ?>
        <p class="error">
            <?="Erreur : ".$res['adresse']?>
        </p>
        <?php
        }
        ?>

        <br>
        <label for="compAdresse">Complément adresse</label>
        <input type="text" name="compAdresse" id="compAdresse" value="<?php if (isset($adresse_client['compAdresse'])) echo $adresse_client['compAdresse']?>"> <p class="contrainte">informations complémentaires</p>


        <br>
        <label for="codePostal">Code postal</label>
        <input type="number" name="codePostal" id="codePostal" size="5" value="<?php if (isset($adresse_client['code_postal'])) echo $adresse_client['code_postal']?>" required>
        <p class="contrainte">Nombre à 5 chiffres</p>
        <?php
        if (isset($res['code_postal'])){
        ?>
        <p class="error">
            <?="Erreur : ".$res['code_postal']?>
        </p>
        <?php
        }
        ?>

        <br>
        <label for="enregistrer">Enregistrer l'adresse</label>
        <input type="checkbox" id="enregistrer" name="enregistrer">

        <br>
        <input type="submit" value="Continuer l'achat">

        </form>
    </body>