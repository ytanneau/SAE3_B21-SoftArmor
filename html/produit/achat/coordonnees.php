<?php
const HOME_GIT = "../../../";

if (!isset($_SESSION)) {
    session_start();
}

// Si pas co, alors retour à l'accueil
if (!isset($_SESSION['logged_in'])) {
    header("location: " . HOME_GIT, );
}


require_once HOME_GIT . ".config.php";
$numEtape = -1;

// $_POST['form'] = le nom du dernier formulaire envoyé par l'utilisateur
if (!isset($_POST['form'])) {
    $numEtape = 1;

    // si y'a pas de produit dans le lien, alors problème (à moins de mettre en place le PANIER)
    if (!isset($_GET['produit'])) {
        header("location: " . HOME_GIT, );
    }

    
    // récup les données adresse préenregistrées
    
    $requete = $pdo->prepare("SELECT adresse, code_postal, complement_adresse FROM client_adresse WHERE id_compte = :id_client");
    $requete->bindValue(":id_client", $_SESSION['id_compte'], PDO::PARAM_STR);
    $requete->execute();
    
    $adresse_client = $requete->fetch(PDO::FETCH_ASSOC);
}





// si $_POST['form'] = 'adresse', alors le dernier formulaire envoyé est le form d'adresse
else if ($_POST['form'] == 'adresse') {

    // gestion du POST des données adresse 

    // met chaînes vides aux colonnes au lieu de null pour éviter erreurs
    if (!isset($_POST['adresse'])) $_POST['adresse'] = "";
    if (!isset($_POST['complement_adresse'])) $_POST['complement_adresse'] = "";
    if (!isset($_POST['code_postal'])) $_POST['code_postal'] = "";

    $fichier = HOME_GIT . 'fonction_compte.php';
    if (file_exists($fichier)) {
        require_once $fichier;
        $erreurs = check_coordonnees($_POST['adresse'], $_POST['code_postal']);


        // enregistrer
        if ($erreurs == [] && isset($_POST['enregistrer']) && $_POST['enregistrer']) {
            sql_insert_adresse_client($pdo, $_SESSION['id_compte'], $_POST['adresse'], $_POST['complement_adresse'], $_POST['code_postal']);
        }

    } else {
        // pas d'accès au fichier fonctions

        $erreurs['fatal'] = true;
        // $fichierLog = __DIR__ . "/erreurs.log";
        // $date = date("Y-m-d H:i:s");
        // file_put_contents($fichierLog, "[$date] Failed find : require_once $fichier;\n", FILE_APPEND);
    }

    
    if ($erreurs == []) {
        // si aucune erreur, alors on passe à l'étape suivante
        $numEtape = 2;

    } else {
        // récup les anciennes valeurs remplies pour préremplir les champs d'adresse
        $adresse_client['adresse'] = $_POST['adresse'];
        $adresse_client['complement_adresse'] = $_POST['complement_adresse'];
        $adresse_client['code_postal'] = $_POST['code_postal'];
    }
}






// si dernier formulaire envoyé est 'bancaire'
else if ($_POST['form'] == 'bancaire') {

    // gestion du POST des données coordonnées bancaires

    if (isset($_POST)) {

        if (!isset($_POST['code_carte'])) $_POST['code_carte'] = "";
        if (!isset($_POST['date_exp'])) $_POST['date_exp'] = "";
        if (!isset($_POST['code_securite'])) $_POST['code_securite'] = "";
    
        $fichier = HOME_GIT . 'fonction_compte.php';
        if (file_exists($fichier)) {
            require_once $fichier;
            $erreurs = check_coordonnees_bancaires($_POST['code_carte'], $_POST['date_exp'], $_POST['code_securite']);
    
    
        } else {
            // pas d'accès au fichier fonctions
    
            $erreurs['fatal'] = true;
            // $fichierLog = __DIR__ . "/erreurs.log";
            // $date = date("Y-m-d H:i:s");
            // file_put_contents($fichierLog, "[$date] Failed find : require_once $fichier;\n", FILE_APPEND);
        }
    
        if ($erreurs != []) {
            // si aucune erreur, alors on passe à l'étape suivante
            $numEtape = 3;
        }
    }
}





if (isset($erreurs) && $erreurs == []) {
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

<?php


// fomulaire coordonnées physiques (adresse)
if ($numEtape == 1) {
?>

        <h1>Entrez vos coordonnées</h1>
        <form action="" method="post">

        <label for="adresse">Adresse</label>
        <input type="text" name="adresse" id="adresse" value="<?=$adresse_client['adresse']?>" required>
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
        <input type="text" name="complement_adresse" id="complement_adresse" value="<?=$adresse_client['complement_adresse']?>"> <p class="contrainte">informations complémentaires</p>


        <br>
        <label for="code_postal">Code postal</label>
        <input type="number" name="code_postal" id="code_postal" size="5" value="<?=$adresse_client['code_postal']?>" required>
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


        <input type="hidden" name="produit" id="produit" required value="<?=$_GET['produit']?>">
        <input type="hidden" name="form" id="form" required value="adresse">
        <br>
        <input type="submit" value="Continuer l'achat">

        </form>

<?php }





// formulaire coordonnées bancaires
else if ($numEtape == 2) {
?>

        <h1>Entrez vos coordonnées bancaires</h1>
        <form action="" method="post">

        <label for="code_carte">Code de carte bancaire</label>
        <input type="text" name="code_carte" id="code_carte" required>
        <p class="contrainte">ex: 1234 5678 9123 4567</p>
        <?php
        if (isset($erreurs['code_carte'])){
        ?>
        <p class="error">
            <?="Erreur : ".$erreurs['code_carte']?>
        </p>
        <?php
        }
        ?>

        <br>
        <label for="date_exp">Date d'expiration de la carte</label>
        <input type="text" name="date_exp" id="date_exp" size="5">
        <p class="contrainte">ex: 12/25, 01/26</p>
        <?php
        if (isset($erreurs['date_exp'])){
        ?>
        <p class="error">
            <?="Erreur : ".$erreurs['date_exp']?>
        </p>
        <?php
        }
        ?>


        <br>
        <label for="code_securite">Code de sécurité</label>
        <input type="number" name="code_securite" id="code_securite" min="100" max="999" required>
        <p class="contrainte">Nombre à 3 chiffres</p>
        <?php
        if (isset($erreurs['code_securite'])){
        ?>
        <p class="error">
            <?="Erreur : ".$erreurs['code_securite']?>
        </p>
        <?php
        }
        ?>

        <input type="hidden" type="number" name="produit" id="produit" required value="<?=$_POST['produit']?>">
        <input type="hidden" name="form" id="form" required value="bancaire">
        <br>
        <input type="submit" value="Effectuer l'achat">

        </form>

<?php
}

// dans le cas où l'utilisateur a déjà bien répondu aux 2 formulaires
else if ($numEtape == 3) {
    ?>
    <h1>Bravo !! Vous avez réussi à acheter un produit !</h1>
    <?php
}

?>
    
    </body>
</html>