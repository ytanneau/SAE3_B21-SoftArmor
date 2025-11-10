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

$requete = $pdo->prepare("SELECT nom_public, prix, tva, description, description_detaillee FROM produit WHERE id_produit = :id_produit");
$requete->bindValue(":id_produit", $_GET['produit'], PDO::PARAM_STR);
$requete->execute();

$produit = $requete->fetch(PDO::FETCH_ASSOC);

$requete = $pdo->prepare("SELECT adresse, code_postal, complement_adresse FROM client_adresse WHERE id_compte = :id_client");
$requete->bindValue(":id_client", $_SESSION['id_compte'], PDO::PARAM_STR);
$requete->execute();

$adresse_client = $requete->fetch(PDO::FETCH_ASSOC);

// gestion du POST
if ($_POST != null){
    if (!isset($_POST['adresse'])) $_POST['adresse'] = "";
    if (!isset($_POST['complement_adresse'])) $_POST['complement_adresse'] = "";
    if (!isset($_POST['code_postal'])) $_POST['code_postal'] = "";

    //echo "présence d'un post";
    //print_r($_ENV);
    $fichier = HOME_GIT . 'fonction_compte.php';
    if (file_exists($fichier)) {
        require_once $fichier;
        $erreurs = check_coordonnees($_POST['adresse'], $_POST['code_postal']);

        // enregistrer
        if ($erreurs == [] && isset($_POST['enregistrer']) && $_POST['enregistrer']) {
            sql_insert_adresse_client($pdo, $_SESSION['id_compte'], $_POST['adresse'], $_POST['complement_adresse'], $_POST['code_postal']);
        }

    } else {
        // echo "erreur 1";

        $erreurs['fatal'] = true;
        // $fichierLog = __DIR__ . "/erreurs.log";
        // $date = date("Y-m-d H:i:s");
        // file_put_contents($fichierLog, "[$date] Failed find : require_once $fichier;\n", FILE_APPEND);
    }
}

if (isset($res) && $res == []) {
    header('Location: ' . 'bancaire.php');
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Alizon - Achat</title>
        <meta charset="UTF-8">
        <meta lang="fr">
    </head>

    <body>
        <h1>Entrez vos coordonnées</h1>
        <form action="" method="post">

        <label for="adresse">Adresse</label>
        <input type="text" name="adresse" id="adresse" value="<?php if (isset($adresse_client['adresse'])) {echo $adresse_client['adresse'];} else if (isset($_POST['adresse'])) {echo $_POST['adresse'];}?>" required>
        <p class="contrainte">ex: 12 rue de la Gare, Paris</p>
        <?php
        if (isset($erreurs['adresse'])){
        ?>
        <p class="error">
            <?="Erreur : ".$erreurs['adresse']?>
        </p>
        <?php
        }
        ?>

        <br>
        <label for="complement_adresse">Complément adresse</label>
        <input type="text" name="complement_adresse" id="complement_adresse" value="<?php if (isset($adresse_client['complement_adresse'])) {echo $adresse_client['complement_adresse'];} else if (isset($_POST['complement_adresse'])) {echo $_POST['complement_adresse'];}?>"> <p class="contrainte">informations complémentaires</p>


        <br>
        <label for="code_postal">Code postal</label>
        <input type="number" name="code_postal" id="code_postal" size="5" value="<?php if (isset($adresse_client['code_postal'])) {echo $adresse_client['code_postal'];} else if (isset($_POST['code_postal'])) {echo $_POST['code_postal'];}?>" required>
        <p class="contrainte">Nombre à 5 chiffres</p>
        <?php
        if (isset($erreurs['code_postal'])){
        ?>
        <p class="error">
            <?="Erreur : ".$erreurs['code_postal']?>
        </p>
        <?php
        }
        ?>

<?php if (!isset($adresse_client['adresse'])) { ?>
        <br>
        <label for="enregistrer">Enregistrer l'adresse</label>
        <input type="checkbox" id="enregistrer" name="enregistrer" >
<?php } ?>
        <br>
        <input type="submit" value="Continuer l'achat">

        </form>
    </body>